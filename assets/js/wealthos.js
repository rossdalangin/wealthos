document.addEventListener('DOMContentLoaded', function() {
	const app = {
		apiRoot: window.wealthosSettings ? window.wealthosSettings.apiRoot : '/wp-json/wealthos/v1/',
		nonce: window.wealthosSettings ? window.wealthosSettings.nonce : '',

		init: function() {
			const container = document.getElementById('wealthos-app');
			if (!container) return;

			this.bindNav();
			this.checkOnboardingAndLoad();
		},

		apiFetch: function(endpoint, method = 'GET', body = null) {
			const headers = {
				'X-WP-Nonce': this.nonce,
				'Content-Type': 'application/json'
			};
			const options = { method: method, headers: headers };
			if (body) {
				options.body = JSON.stringify(body);
			}
			return fetch(this.apiRoot + endpoint, options).then(res => res.json());
		},

		checkOnboardingAndLoad: function() {
			const contentDiv = document.getElementById('wealthos-tab-content');
			const self = this;

			this.apiFetch('profile').then(profile => {
				if (!profile.onboarding_completed) {
					self.renderOnboarding(contentDiv);
				} else {
					self.loadSummary();
				}
			});
		},

		bindNav: function() {
			const navBtns = document.querySelectorAll('.wealthos-nav-btn');
			const self = this;

			navBtns.forEach(btn => {
				btn.addEventListener('click', function() {
					navBtns.forEach(b => b.classList.remove('active'));
					this.classList.add('active');

					const tab = this.getAttribute('data-tab');
					self.loadTabContent(tab);
				});
			});
		},

		loadTabContent: function(tab) {
			const contentDiv = document.getElementById('wealthos-tab-content');
			if (!contentDiv) return;

			contentDiv.innerHTML = '<div class="wealthos-card"><p>Loading module...</p></div>';

			switch (tab) {
				case 'overview':
					this.loadSummary();
					break;
				case 'onboarding':
					this.renderOnboarding(contentDiv);
					break;
				case 'income':
					this.loadIncome(contentDiv);
					break;
				case 'expenses':
					this.loadExpenses(contentDiv);
					break;
				case 'budgets':
					this.loadBudgets(contentDiv);
					break;
				case 'debts':
					this.loadDebts(contentDiv);
					break;
				case 'savings':
					this.loadSavings(contentDiv);
					break;
				case 'investments':
					this.loadInvestments(contentDiv);
					break;
				case 'assets':
					this.loadAssets(contentDiv);
					break;
				case 'business':
					this.loadBusiness(contentDiv);
					break;
				case 'risk':
					this.loadRisk(contentDiv);
					break;
				case 'actions':
					this.loadActions(contentDiv);
					break;
				case 'compounding':
					this.loadCompoundingCalculator(contentDiv);
					break;
				default:
					this.loadSummary();
			}
		},

		// --- ONBOARDING WIZARD ---
		renderOnboarding: function(container) {
			container.innerHTML = `
				<div class="wealthos-card" style="max-width: 700px; margin: 0 auto;">
					<h2>Welcome to WealthOS</h2>
					<p style="color: var(--wealthos-text-muted);">
						WealthOS helps you understand your money, improve cash flow, build savings, manage debt, invest consistently, grow assets, and track your progress toward financial goals.
					</p>

					<form id="wealthos-onboarding-form" onsubmit="WealthOSApp.saveOnboarding(event)">
						<div class="wealthos-grid-2">
							<div class="wealthos-form-group">
								<label>Age Range</label>
								<select id="ob-age">
									<option value="18-24">18-24</option>
									<option value="25-34" selected>25-34</option>
									<option value="35-44">35-44</option>
									<option value="45-54">45-54</option>
									<option value="55+">55+</option>
								</select>
							</div>
							<div class="wealthos-form-group">
								<label>Currency</label>
								<input type="text" id="ob-currency" value="$">
							</div>
						</div>

						<div class="wealthos-grid-2">
							<div class="wealthos-form-group">
								<label>Employment Type</label>
								<select id="ob-emp">
									<option value="Employed">Employed / Salaried</option>
									<option value="Freelancer">Freelancer / Contractor</option>
									<option value="Entrepreneur">Entrepreneur / Business Owner</option>
									<option value="Other">Other</option>
								</select>
							</div>
							<div class="wealthos-form-group">
								<label>Risk Tolerance</label>
								<select id="ob-risk">
									<option value="conservative">Conservative</option>
									<option value="moderate" selected>Moderate</option>
									<option value="aggressive">Aggressive</option>
								</select>
							</div>
						</div>

						<div class="wealthos-form-group">
							<label>Primary Financial Goal</label>
							<input type="text" id="ob-goal" value="Build $100k Net Worth & Eliminate Debt">
						</div>

						<button type="submit" class="wealthos-btn" style="width: 100%; margin-top: 10px;">Complete Setup & Enter System</button>
					</form>
				</div>
			`;
		},

		saveOnboarding: function(e) {
			e.preventDefault();
			const data = {
				age_range: document.getElementById('ob-age').value,
				currency: document.getElementById('ob-currency').value,
				employment_type: document.getElementById('ob-emp').value,
				risk_tolerance: document.getElementById('ob-risk').value,
				financial_goal: document.getElementById('ob-goal').value,
				onboarding_completed: 1
			};

			const self = this;
			this.apiFetch('profile', 'POST', data).then(() => {
				self.loadSummary();
			});
		},

		// --- OVERVIEW DASHBOARD ---
		loadSummary: function() {
			const contentDiv = document.getElementById('wealthos-tab-content');
			const self = this;

			this.apiFetch('dashboard-summary').then(data => {
				if (contentDiv) {
					self.renderOverview(contentDiv, data);
				}
			});
		},

		renderOverview: function(container, data) {
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			let html = `
				<div class="wealthos-grid-4">
					<div class="wealthos-card">
						<h3>Monthly Income</h3>
						<div class="wealthos-stat-value">${symbol}${data.income.monthly_total.toLocaleString()}</div>
						<div class="wealthos-stat-sub">Annual: ${symbol}${data.income.annual_total.toLocaleString()}</div>
					</div>
					<div class="wealthos-card">
						<h3>Monthly Expenses</h3>
						<div class="wealthos-stat-value">${symbol}${data.expenses.monthly_total.toLocaleString()}</div>
						<div class="wealthos-stat-sub">Essential: ${symbol}${data.expenses.essential_monthly.toLocaleString()}</div>
					</div>
					<div class="wealthos-card">
						<h3>Cash Flow Surplus</h3>
						<div class="wealthos-stat-value" style="color: ${data.cash_flow.is_deficit ? 'var(--wealthos-danger)' : 'var(--wealthos-success)'}">
							${symbol}${data.cash_flow.surplus.toLocaleString()}
						</div>
						<div class="wealthos-stat-sub">Savings Rate: ${data.cash_flow.savings_rate}%</div>
					</div>
					<div class="wealthos-card">
						<h3>Net Worth</h3>
						<div class="wealthos-stat-value">${symbol}${data.net_worth.net_worth.toLocaleString()}</div>
						<div class="wealthos-stat-sub">Growth: ${data.net_worth.net_worth_growth_pct}%</div>
					</div>
				</div>

				<div class="wealthos-grid-2">
					<div class="wealthos-card">
						<h3>WealthOS Score</h3>
						<div style="display: flex; align-items: center; gap: 20px;">
							<div class="wealthos-score-badge">${data.wealth_score.score}</div>
							<div>
								<h4 style="margin: 0; font-size: 18px;">Status: ${data.wealth_score.status}</h4>
								<p style="margin: 4px 0 0 0; font-size: 13px; color: var(--wealthos-text-muted);">${data.wealth_score.disclaimer}</p>
							</div>
						</div>
					</div>

					<div class="wealthos-card">
						<h3>#1 Wealth Bottleneck</h3>
						<h4 style="margin: 0 0 8px 0; color: var(--wealthos-danger);">${data.bottleneck.title}</h4>
						<p style="margin: 0 0 12px 0; font-size: 14px;">${data.bottleneck.description}</p>
						<ul style="margin: 0; padding-left: 20px; font-size: 13px;">
							${data.bottleneck.recommendations.map(r => `<li>${r}</li>`).join('')}
						</ul>
					</div>
				</div>
			`;

			container.innerHTML = html;
		},

		// --- INCOME MODULE ---
		loadIncome: function(container) {
			const self = this;
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			this.apiFetch('income').then(items => {
				let rows = items.map(item => `
					<tr>
						<td><strong>${item.name}</strong></td>
						<td>${item.category}</td>
						<td>${symbol}${parseFloat(item.amount).toLocaleString()}</td>
						<td>${item.frequency}</td>
						<td><button class="wealthos-btn" style="background: var(--wealthos-danger); padding: 4px 8px; font-size: 12px;" onclick="WealthOSApp.deleteItem('income', ${item.id})">Delete</button></td>
					</tr>
				`).join('');

				container.innerHTML = `
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Add Income Stream</h3>
							<form onsubmit="WealthOSApp.addIncome(event)">
								<div class="wealthos-form-group">
									<label>Name / Source</label>
									<input type="text" id="inc-name" required placeholder="Main Salary">
								</div>
								<div class="wealthos-form-group">
									<label>Category</label>
									<select id="inc-category">
										<option value="Salary">Salary</option>
										<option value="Freelance">Freelance</option>
										<option value="Business">Business</option>
										<option value="Investments">Investments</option>
										<option value="Side Hustle">Side Hustle</option>
									</select>
								</div>
								<div class="wealthos-form-group">
									<label>Amount (${symbol})</label>
									<input type="number" id="inc-amount" step="0.01" required placeholder="5000">
								</div>
								<div class="wealthos-form-group">
									<label>Frequency</label>
									<select id="inc-freq">
										<option value="monthly">Monthly</option>
										<option value="annually">Annually</option>
										<option value="weekly">Weekly</option>
									</select>
								</div>
								<button type="submit" class="wealthos-btn">Save Income</button>
							</form>
						</div>

						<div class="wealthos-card">
							<h3>Active Income Streams</h3>
							<table class="wealthos-table">
								<thead>
									<tr>
										<th>Name</th>
										<th>Category</th>
										<th>Amount</th>
										<th>Freq</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
									${rows.length ? rows : '<tr><td colspan="5">No income items logged yet.</td></tr>'}
								</tbody>
							</table>
						</div>
					</div>
				`;
			});
		},

		addIncome: function(e) {
			e.preventDefault();
			const data = {
				name: document.getElementById('inc-name').value,
				category: document.getElementById('inc-category').value,
				amount: parseFloat(document.getElementById('inc-amount').value),
				frequency: document.getElementById('inc-freq').value,
			};
			const self = this;
			this.apiFetch('income', 'POST', data).then(() => {
				self.loadIncome(document.getElementById('wealthos-tab-content'));
			});
		},

		// --- EXPENSES MODULE ---
		loadExpenses: function(container) {
			const self = this;
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			this.apiFetch('expenses').then(items => {
				let rows = items.map(item => `
					<tr>
						<td><strong>${item.name}</strong></td>
						<td>${item.category}</td>
						<td>${symbol}${parseFloat(item.amount).toLocaleString()}</td>
						<td>${item.is_essential ? 'Essential' : 'Discretionary'}</td>
						<td><button class="wealthos-btn" style="background: var(--wealthos-danger); padding: 4px 8px; font-size: 12px;" onclick="WealthOSApp.deleteItem('expenses', ${item.id})">Delete</button></td>
					</tr>
				`).join('');

				container.innerHTML = `
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Add Expense</h3>
							<form onsubmit="WealthOSApp.addExpense(event)">
								<div class="wealthos-form-group">
									<label>Expense Name</label>
									<input type="text" id="exp-name" required placeholder="Rent / Mortgage">
								</div>
								<div class="wealthos-form-group">
									<label>Category</label>
									<select id="exp-category">
										<option value="Housing">Housing</option>
										<option value="Food">Food</option>
										<option value="Utilities">Utilities</option>
										<option value="Transportation">Transportation</option>
										<option value="Subscriptions">Subscriptions</option>
										<option value="Entertainment">Entertainment</option>
									</select>
								</div>
								<div class="wealthos-form-group">
									<label>Amount (${symbol})</label>
									<input type="number" id="exp-amount" step="0.01" required placeholder="1200">
								</div>
								<div class="wealthos-form-group">
									<label>Type</label>
									<select id="exp-essential">
										<option value="1">Essential</option>
										<option value="0">Discretionary</option>
									</select>
								</div>
								<button type="submit" class="wealthos-btn">Save Expense</button>
							</form>
						</div>

						<div class="wealthos-card">
							<h3>Expense Tracker</h3>
							<table class="wealthos-table">
								<thead>
									<tr>
										<th>Name</th>
										<th>Category</th>
										<th>Amount</th>
										<th>Type</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
									${rows.length ? rows : '<tr><td colspan="5">No expense items logged yet.</td></tr>'}
								</tbody>
							</table>
						</div>
					</div>
				`;
			});
		},

		addExpense: function(e) {
			e.preventDefault();
			const data = {
				name: document.getElementById('exp-name').value,
				category: document.getElementById('exp-category').value,
				amount: parseFloat(document.getElementById('exp-amount').value),
				is_essential: parseInt(document.getElementById('exp-essential').value),
			};
			const self = this;
			this.apiFetch('expenses', 'POST', data).then(() => {
				self.loadExpenses(document.getElementById('wealthos-tab-content'));
			});
		},

		// --- DEBTS MODULE ---
		loadDebts: function(container) {
			const self = this;
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			this.apiFetch('debts').then(items => {
				let rows = items.map(item => `
					<tr>
						<td><strong>${item.name}</strong></td>
						<td>${symbol}${parseFloat(item.balance).toLocaleString()}</td>
						<td>${item.interest_rate}%</td>
						<td>${symbol}${parseFloat(item.minimum_payment).toLocaleString()}</td>
						<td><button class="wealthos-btn" style="background: var(--wealthos-danger); padding: 4px 8px; font-size: 12px;" onclick="WealthOSApp.deleteItem('debts', ${item.id})">Delete</button></td>
					</tr>
				`).join('');

				container.innerHTML = `
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Add Debt Balance</h3>
							<form onsubmit="WealthOSApp.addDebt(event)">
								<div class="wealthos-form-group">
									<label>Debt Name</label>
									<input type="text" id="debt-name" required placeholder="Credit Card">
								</div>
								<div class="wealthos-form-group">
									<label>Current Balance (${symbol})</label>
									<input type="number" id="debt-bal" step="0.01" required placeholder="3000">
								</div>
								<div class="wealthos-form-group">
									<label>Interest Rate (%)</label>
									<input type="number" id="debt-rate" step="0.1" required placeholder="18.9">
								</div>
								<div class="wealthos-form-group">
									<label>Minimum Payment (${symbol})</label>
									<input type="number" id="debt-min" step="0.01" required placeholder="75">
								</div>
								<button type="submit" class="wealthos-btn">Save Debt</button>
							</form>
						</div>

						<div class="wealthos-card">
							<h3>Debt Tracker (Avalanche / Snowball Ready)</h3>
							<table class="wealthos-table">
								<thead>
									<tr>
										<th>Name</th>
										<th>Balance</th>
										<th>Rate</th>
										<th>Min Pay</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
									${rows.length ? rows : '<tr><td colspan="5">No debt items logged yet.</td></tr>'}
								</tbody>
							</table>
						</div>
					</div>
				`;
			});
		},

		addDebt: function(e) {
			e.preventDefault();
			const data = {
				name: document.getElementById('debt-name').value,
				balance: parseFloat(document.getElementById('debt-bal').value),
				interest_rate: parseFloat(document.getElementById('debt-rate').value),
				minimum_payment: parseFloat(document.getElementById('debt-min').value),
			};
			const self = this;
			this.apiFetch('debts', 'POST', data).then(() => {
				self.loadDebts(document.getElementById('wealthos-tab-content'));
			});
		},

		// --- SAVINGS MODULE ---
		loadSavings: function(container) {
			const self = this;
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			this.apiFetch('savings').then(items => {
				let rows = items.map(item => `
					<tr>
						<td><strong>${item.goal_name}</strong></td>
						<td>${symbol}${parseFloat(item.target_amount).toLocaleString()}</td>
						<td>${symbol}${parseFloat(item.current_amount).toLocaleString()}</td>
						<td><button class="wealthos-btn" style="background: var(--wealthos-danger); padding: 4px 8px; font-size: 12px;" onclick="WealthOSApp.deleteItem('savings', ${item.id})">Delete</button></td>
					</tr>
				`).join('');

				container.innerHTML = `
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Add Savings Goal</h3>
							<form onsubmit="WealthOSApp.addSavings(event)">
								<div class="wealthos-form-group">
									<label>Goal Name</label>
									<input type="text" id="sav-name" required placeholder="Emergency Fund">
								</div>
								<div class="wealthos-form-group">
									<label>Target Amount (${symbol})</label>
									<input type="number" id="sav-target" step="0.01" required placeholder="10000">
								</div>
								<div class="wealthos-form-group">
									<label>Current Amount (${symbol})</label>
									<input type="number" id="sav-current" step="0.01" required placeholder="2500">
								</div>
								<button type="submit" class="wealthos-btn">Save Goal</button>
							</form>
						</div>

						<div class="wealthos-card">
							<h3>Savings Goals</h3>
							<table class="wealthos-table">
								<thead>
									<tr>
										<th>Goal</th>
										<th>Target</th>
										<th>Current</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
									${rows.length ? rows : '<tr><td colspan="4">No savings goals logged yet.</td></tr>'}
								</tbody>
							</table>
						</div>
					</div>
				`;
			});
		},

		addSavings: function(e) {
			e.preventDefault();
			const data = {
				goal_name: document.getElementById('sav-name').value,
				target_amount: parseFloat(document.getElementById('sav-target').value),
				current_amount: parseFloat(document.getElementById('sav-current').value),
			};
			const self = this;
			this.apiFetch('savings', 'POST', data).then(() => {
				self.loadSavings(document.getElementById('wealthos-tab-content'));
			});
		},

		// --- INVESTMENTS MODULE ---
		loadInvestments: function(container) {
			const self = this;
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			this.apiFetch('investments').then(items => {
				let rows = items.map(item => `
					<tr>
						<td><strong>${item.name}</strong></td>
						<td>${item.asset_class}</td>
						<td>${symbol}${parseFloat(item.current_value).toLocaleString()}</td>
						<td><button class="wealthos-btn" style="background: var(--wealthos-danger); padding: 4px 8px; font-size: 12px;" onclick="WealthOSApp.deleteItem('investments', ${item.id})">Delete</button></td>
					</tr>
				`).join('');

				container.innerHTML = `
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Add Investment</h3>
							<form onsubmit="WealthOSApp.addInvestment(event)">
								<div class="wealthos-form-group">
									<label>Investment Name</label>
									<input type="text" id="inv-name" required placeholder="S&P 500 ETF">
								</div>
								<div class="wealthos-form-group">
									<label>Asset Class</label>
									<select id="inv-class">
										<option value="Stocks">Stocks / ETFs</option>
										<option value="Bonds">Bonds</option>
										<option value="Real Estate">Real Estate</option>
										<option value="Crypto">Crypto</option>
									</select>
								</div>
								<div class="wealthos-form-group">
									<label>Current Value (${symbol})</label>
									<input type="number" id="inv-value" step="0.01" required placeholder="15000">
								</div>
								<button type="submit" class="wealthos-btn">Save Investment</button>
							</form>
						</div>

						<div class="wealthos-card">
							<h3>Investment Portfolio</h3>
							<table class="wealthos-table">
								<thead>
									<tr>
										<th>Name</th>
										<th>Class</th>
										<th>Current Value</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
									${rows.length ? rows : '<tr><td colspan="4">No investments logged yet.</td></tr>'}
								</tbody>
							</table>
						</div>
					</div>
				`;
			});
		},

		addInvestment: function(e) {
			e.preventDefault();
			const data = {
				name: document.getElementById('inv-name').value,
				asset_class: document.getElementById('inv-class').value,
				current_value: parseFloat(document.getElementById('inv-value').value),
			};
			const self = this;
			this.apiFetch('investments', 'POST', data).then(() => {
				self.loadInvestments(document.getElementById('wealthos-tab-content'));
			});
		},

		// --- PRODUCTIVE ASSETS MODULE ---
		loadAssets: function(container) {
			const self = this;
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			this.apiFetch('assets').then(items => {
				let rows = items.map(item => `
					<tr>
						<td><strong>${item.name}</strong></td>
						<td>${item.category}</td>
						<td>${symbol}${parseFloat(item.estimated_value).toLocaleString()}</td>
						<td>${symbol}${parseFloat(item.monthly_income).toLocaleString()}/mo</td>
						<td><button class="wealthos-btn" style="background: var(--wealthos-danger); padding: 4px 8px; font-size: 12px;" onclick="WealthOSApp.deleteItem('assets', ${item.id})">Delete</button></td>
					</tr>
				`).join('');

				container.innerHTML = `
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Add Productive Asset</h3>
							<form onsubmit="WealthOSApp.addAsset(event)">
								<div class="wealthos-form-group">
									<label>Asset Name</label>
									<input type="text" id="ast-name" required placeholder="Rental Property A">
								</div>
								<div class="wealthos-form-group">
									<label>Category</label>
									<select id="ast-category">
										<option value="Real Estate">Real Estate</option>
										<option value="Business">Business</option>
										<option value="Intellectual Property">Intellectual Property</option>
										<option value="Equipment">Equipment</option>
									</select>
								</div>
								<div class="wealthos-form-group">
									<label>Estimated Value (${symbol})</label>
									<input type="number" id="ast-val" step="0.01" required placeholder="250000">
								</div>
								<div class="wealthos-form-group">
									<label>Monthly Cash Flow / Income (${symbol})</label>
									<input type="number" id="ast-income" step="0.01" placeholder="800">
								</div>
								<button type="submit" class="wealthos-btn">Save Productive Asset</button>
							</form>
						</div>

						<div class="wealthos-card">
							<h3>Productive Assets</h3>
							<table class="wealthos-table">
								<thead>
									<tr>
										<th>Name</th>
										<th>Category</th>
										<th>Value</th>
										<th>Income</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
									${rows.length ? rows : '<tr><td colspan="5">No productive assets logged yet.</td></tr>'}
								</tbody>
							</table>
						</div>
					</div>
				`;
			});
		},

		addAsset: function(e) {
			e.preventDefault();
			const data = {
				name: document.getElementById('ast-name').value,
				category: document.getElementById('ast-category').value,
				estimated_value: parseFloat(document.getElementById('ast-val').value),
				monthly_income: parseFloat(document.getElementById('ast-income').value) || 0,
			};
			const self = this;
			this.apiFetch('assets', 'POST', data).then(() => {
				self.loadAssets(document.getElementById('wealthos-tab-content'));
			});
		},

		// --- BUSINESS MODULE ---
		loadBusiness: function(container) {
			const self = this;
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			this.apiFetch('business').then(data => {
				const m = data.metrics || {};
				container.innerHTML = `
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Log Business Performance</h3>
							<form onsubmit="WealthOSApp.saveBusiness(event)">
								<div class="wealthos-form-group">
									<label>Monthly Revenue (${symbol})</label>
									<input type="number" id="biz-rev" value="${m.revenue || ''}" required placeholder="12000">
								</div>
								<div class="wealthos-form-group">
									<label>Monthly Operating Expenses (${symbol})</label>
									<input type="number" id="biz-exp" value="${m.expenses || ''}" required placeholder="4000">
								</div>
								<div class="wealthos-form-group">
									<label>Owner Compensation / Draw (${symbol})</label>
									<input type="number" id="biz-draw" value="${m.owner_compensation || ''}" placeholder="5000">
								</div>
								<button type="submit" class="wealthos-btn">Update Business Metrics</button>
							</form>
						</div>

						<div class="wealthos-card">
							<h3>Business Metrics Summary</h3>
							<div class="wealthos-grid-2">
								<div>
									<div class="wealthos-stat-sub">Monthly Revenue</div>
									<div class="wealthos-stat-value" style="font-size: 22px;">${symbol}${parseFloat(m.revenue || 0).toLocaleString()}</div>
								</div>
								<div>
									<div class="wealthos-stat-sub">Monthly Profit</div>
									<div class="wealthos-stat-value" style="font-size: 22px; color: var(--wealthos-accent);">${symbol}${parseFloat(m.profit || 0).toLocaleString()}</div>
								</div>
							</div>
						</div>
					</div>
				`;
			});
		},

		saveBusiness: function(e) {
			e.preventDefault();
			const data = {
				revenue: parseFloat(document.getElementById('biz-rev').value),
				expenses: parseFloat(document.getElementById('biz-exp').value),
				owner_compensation: parseFloat(document.getElementById('biz-draw').value) || 0,
			};
			const self = this;
			this.apiFetch('business-metric', 'POST', data).then(() => {
				self.loadBusiness(document.getElementById('wealthos-tab-content'));
			});
		},

		// --- RISK MODULE ---
		loadRisk: function(container) {
			const self = this;

			this.apiFetch('risk').then(data => {
				let rows = data.checklist.map(item => `
					<tr>
						<td><strong>${item.title}</strong></td>
						<td>${item.category}</td>
						<td>
							<select onchange="WealthOSApp.updateRiskItem('${item.item_key}', this.value, '${item.title}', '${item.category}')">
								<option value="needs_attention" ${item.status === 'needs_attention' ? 'selected' : ''}>Needs Attention</option>
								<option value="complete" ${item.status === 'complete' ? 'selected' : ''}>Complete</option>
							</select>
						</td>
					</tr>
				`).join('');

				container.innerHTML = `
					<div class="wealthos-card">
						<h3>Financial Risk Management Checklist</h3>
						<p>Risk Score: <strong>${data.risk_score} / 100 (${data.risk_label} Risk)</strong></p>
						<table class="wealthos-table">
							<thead>
								<tr>
									<th>Checklist Item</th>
									<th>Category</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								${rows}
							</tbody>
						</table>
					</div>
				`;
			});
		},

		updateRiskItem: function(key, status, title, category) {
			const data = { item_key: key, status: status, title: title, category: category };
			const self = this;
			this.apiFetch('risk', 'POST', data).then(() => {
				self.loadRisk(document.getElementById('wealthos-tab-content'));
			});
		},

		// --- ACTIONS & ROADMAP MODULE ---
		loadActions: function(container) {
			const self = this;

			this.apiFetch('actions').then(data => {
				let rows = data.system_actions.map(act => `
					<tr>
						<td><strong>${act.title}</strong></td>
						<td>${act.reason}</td>
						<td>${act.priority}</td>
					</tr>
				`).join('');

				container.innerHTML = `
					<div class="wealthos-card">
						<h3>"What Should I Do Next?" — Prioritized Action Plan</h3>
						<table class="wealthos-table">
							<thead>
								<tr>
									<th>Recommended Action</th>
									<th>System Reason</th>
									<th>Priority</th>
								</tr>
							</thead>
							<tbody>
								${rows.length ? rows : '<tr><td colspan="3">Your financial system is fully optimized!</td></tr>'}
							</tbody>
						</table>
					</div>
				`;
			});
		},

		// --- COMPOUNDING CALCULATOR ---
		loadCompoundingCalculator: function(container) {
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			container.innerHTML = `
				<div class="wealthos-card" style="max-width: 600px; margin: 0 auto;">
					<h3>Compound Growth Calculator</h3>
					<div class="wealthos-form-group">
						<label>Initial Investment (${symbol})</label>
						<input type="number" id="calc-initial" value="1000">
					</div>
					<div class="wealthos-form-group">
						<label>Monthly Contribution (${symbol})</label>
						<input type="number" id="calc-monthly" value="500">
					</div>
					<div class="wealthos-form-group">
						<label>Annual Return Assumption (%)</label>
						<input type="number" id="calc-return" value="7.0" step="0.1">
					</div>
					<div class="wealthos-form-group">
						<label>Years to Invest</label>
						<input type="number" id="calc-years" value="20">
					</div>
					<button class="wealthos-btn" onclick="WealthOSApp.runCompoundingCalc()">Calculate Growth</button>

					<div id="calc-results" style="margin-top: 20px; display: none;">
						<hr style="border: 0; border-top: 1px solid var(--wealthos-border); margin-bottom: 16px;">
						<div class="wealthos-grid-2">
							<div>
								<div class="wealthos-stat-sub">Total Contributed</div>
								<div class="wealthos-stat-value" id="calc-contrib-out" style="font-size: 20px;"></div>
							</div>
							<div>
								<div class="wealthos-stat-sub">Estimated Final Value</div>
								<div class="wealthos-stat-value" id="calc-final-out" style="font-size: 20px; color: var(--wealthos-accent);"></div>
							</div>
						</div>
					</div>
				</div>
			`;
		},

		runCompoundingCalc: function() {
			const initial = parseFloat(document.getElementById('calc-initial').value) || 0;
			const monthly = parseFloat(document.getElementById('calc-monthly').value) || 0;
			const rate = parseFloat(document.getElementById('calc-return').value) || 0;
			const years = parseInt(document.getElementById('calc-years').value) || 1;

			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';
			const months = years * 12;
			const mRate = (rate / 100) / 12;

			const contrib = initial + (monthly * months);
			let future = initial * Math.pow(1 + mRate, months);
			if (mRate > 0) {
				future += monthly * ((Math.pow(1 + mRate, months) - 1) / mRate);
			} else {
				future += monthly * months;
			}

			document.getElementById('calc-contrib-out').innerText = symbol + Math.round(contrib).toLocaleString();
			document.getElementById('calc-final-out').innerText = symbol + Math.round(future).toLocaleString();
			document.getElementById('calc-results').style.display = 'block';
		},

		deleteItem: function(module, id) {
			if (!confirm('Are you sure you want to delete this item?')) return;
			const self = this;
			this.apiFetch(module + '/' + id, 'DELETE').then(() => {
				self.loadTabContent(module);
			});
		}
	};

	window.WealthOSApp = app;
	app.init();
});
