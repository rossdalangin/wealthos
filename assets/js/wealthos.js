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
				case 'roadmap':
					this.loadRoadmap(contentDiv);
					break;
				case 'fi':
					this.loadFI(contentDiv);
					break;
				case 'scenario':
					this.loadScenarioPlanner(contentDiv);
					break;
				case 'compounding':
					this.loadCompoundingCalculator(contentDiv);
					break;
				case 'reports':
					this.loadReports(contentDiv);
					break;
				default:
					this.loadSummary();
			}
		},

		// --- ONBOARDING WIZARD ---
		renderOnboarding: function(container) {
			container.innerHTML = `
				<div class="wealthos-card" style="max-width: 700px; margin: 0 auto;">
					<h2>Welcome to WealthOS — Onboarding Setup</h2>
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
								<label>Currency Symbol</label>
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

			// Render Donut Chart SVG for Asset Allocation
			const allocPct = data.investments.allocation_pct || {};
			const allocKeys = Object.keys(allocPct);
			let donutSvg = '';

			if (allocKeys.length > 0) {
				const colors = ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899'];
				let startAngle = 0;
				let slices = '';
				let legend = '';

				allocKeys.forEach((key, idx) => {
					const pct = allocPct[key];
					const angle = (pct / 100) * 360;
					const color = colors[idx % colors.length];

					const x1 = 50 + 40 * Math.cos(Math.PI * startAngle / 180);
					const y1 = 50 + 40 * Math.sin(Math.PI * startAngle / 180);
					const endAngle = startAngle + angle;
					const x2 = 50 + 40 * Math.cos(Math.PI * endAngle / 180);
					const y2 = 50 + 40 * Math.sin(Math.PI * endAngle / 180);
					const largeArc = angle > 180 ? 1 : 0;

					if (pct >= 99.9) {
						slices += `<circle cx="50" cy="50" r="40" fill="${color}" />`;
					} else {
						slices += `<path d="M50,50 L${x1},${y1} A40,40 0 ${largeArc},1 ${x2},${y2} Z" fill="${color}" />`;
					}

					legend += `<div style="display:flex; align-items:center; gap:8px; font-size:12px; margin-top:4px;">
						<span style="width:12px; height:12px; background:${color}; border-radius:2px; display:inline-block;"></span>
						<span>${key}: ${pct}%</span>
					</div>`;

					startAngle = endAngle;
				});

				donutSvg = `
					<div style="display:flex; align-items:center; gap:20px; margin-top:12px;">
						<svg viewBox="0 0 100 100" style="width:100px; height:100px; border-radius:50%;">
							${slices}
							<circle cx="50" cy="50" r="22" fill="#ffffff" />
						</svg>
						<div>${legend}</div>
					</div>
				`;
			} else {
				donutSvg = '<p style="font-size:13px; color:var(--wealthos-text-muted);">No investment allocation logged yet.</p>';
			}

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
						<div style="margin-top: 16px; font-size: 13px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
							<div>Cash Flow: ${data.wealth_score.cash_flow_score}/20</div>
							<div>Emergency: ${data.wealth_score.emergency_score}/20</div>
							<div>Debt Burden: ${data.wealth_score.debt_score}/20</div>
							<div>Investing: ${data.wealth_score.investing_score}/20</div>
							<div>Risk Protection: ${data.wealth_score.risk_protection_score}/20</div>
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

				<div class="wealthos-grid-2">
					<div class="wealthos-card">
						<h3>Asset Allocation</h3>
						${donutSvg}
					</div>
					<div class="wealthos-card">
						<h3>Emergency Reserve Protection</h3>
						<h4 style="margin: 0 0 8px 0;">${data.emergency.stage}</h4>
						<div style="background:#e2e8f0; border-radius:10px; height:12px; width:100%; overflow:hidden;">
							<div style="background:var(--wealthos-accent); height:100%; width:${data.emergency.percentage_completed}%;"></div>
						</div>
						<p style="margin:8px 0 0 0; font-size:13px; color:var(--wealthos-text-muted);">
							Current: ${symbol}${data.emergency.current_amount.toLocaleString()} / Target: ${symbol}${data.emergency.target_amount.toLocaleString()} (${data.emergency.percentage_completed}%)
						</p>
					</div>
				</div>
			`;

			container.innerHTML = html;
		},

		// --- STAGE 1: INCOME MODULE ---
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
					<div class="wealthos-card" style="margin-bottom:20px; background:#eff6ff; border-left:4px solid #3b82f6;">
						<h4 style="margin:0 0 4px 0; color:#1e40af;">Stage 1: Income Module Goal</h4>
						<p style="margin:0; font-size:13px; color:#1e3a8a;">Log all revenue streams (Salary, Freelance, Side Hustles, Investments) to establish gross monthly earning power.</p>
					</div>
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Add Income Stream</h3>
							<form onsubmit="WealthOSApp.addIncome(event)">
								<div class="wealthos-form-group">
									<label>Name / Source</label>
									<input type="text" id="inc-name" required placeholder="Senior Software Engineer Salary">
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
									<input type="number" id="inc-amount" step="0.01" required placeholder="5500">
								</div>
								<div class="wealthos-form-group">
									<label>Frequency</label>
									<select id="inc-freq">
										<option value="monthly">Monthly</option>
										<option value="annually">Annually</option>
										<option value="weekly">Weekly</option>
										<option value="biweekly">Bi-weekly</option>
									</select>
								</div>
								<button type="submit" class="wealthos-btn">Save Income Stream</button>
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

		// --- STAGE 3: EXPENSES MODULE ---
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
					<div class="wealthos-card" style="margin-bottom:20px; background:#eff6ff; border-left:4px solid #3b82f6;">
						<h4 style="margin:0 0 4px 0; color:#1e40af;">Stage 3: Expense Control Goal</h4>
						<p style="margin:0; font-size:13px; color:#1e3a8a;">Audit essential baseline needs vs discretionary expenses to eliminate leaks and maximize cash-flow surplus.</p>
					</div>
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Add Expense Item</h3>
							<form onsubmit="WealthOSApp.addExpense(event)">
								<div class="wealthos-form-group">
									<label>Expense Name</label>
									<input type="text" id="exp-name" required placeholder="Apartment Rent">
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
									<input type="number" id="exp-amount" step="0.01" required placeholder="1400">
								</div>
								<div class="wealthos-form-group">
									<label>Expense Type</label>
									<select id="exp-essential">
										<option value="1">Essential (Needs)</option>
										<option value="0">Discretionary (Wants)</option>
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

		// --- STAGE 6: DEBTS MODULE ---
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
					<div class="wealthos-card" style="margin-bottom:20px; background:#eff6ff; border-left:4px solid #3b82f6;">
						<h4 style="margin:0 0 4px 0; color:#1e40af;">Stage 6: Debt Reduction Goal</h4>
						<p style="margin:0; font-size:13px; color:#1e3a8a;">Eliminate high-interest debt using Debt Avalanche (lowest interest cost) or Debt Snowball (rapid momentum).</p>
					</div>
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Add Debt Balance</h3>
							<form onsubmit="WealthOSApp.addDebt(event)">
								<div class="wealthos-form-group">
									<label>Debt Name</label>
									<input type="text" id="debt-name" required placeholder="Rewards Credit Card">
								</div>
								<div class="wealthos-form-group">
									<label>Current Balance (${symbol})</label>
									<input type="number" id="debt-bal" step="0.01" required placeholder="4500">
								</div>
								<div class="wealthos-form-group">
									<label>Interest Rate (%)</label>
									<input type="number" id="debt-rate" step="0.1" required placeholder="21.5">
								</div>
								<div class="wealthos-form-group">
									<label>Minimum Payment (${symbol})</label>
									<input type="number" id="debt-min" step="0.01" required placeholder="120">
								</div>
								<button type="submit" class="wealthos-btn">Save Debt Balance</button>
							</form>
						</div>

						<div class="wealthos-card">
							<h3>Debt Tracker (Avalanche / Snowball Strategy)</h3>
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

		// --- STAGE 7: SAVINGS MODULE ---
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
					<div class="wealthos-card" style="margin-bottom:20px; background:#eff6ff; border-left:4px solid #3b82f6;">
						<h4 style="margin:0 0 4px 0; color:#1e40af;">Stage 7: Savings Goals Goal</h4>
						<p style="margin:0; font-size:13px; color:#1e3a8a;">Accumulate dedicated cash reserves for specific milestones (house down payment, emergency reserve, vehicle).</p>
					</div>
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Add Savings Goal</h3>
							<form onsubmit="WealthOSApp.addSavings(event)">
								<div class="wealthos-form-group">
									<label>Goal Name</label>
									<input type="text" id="sav-name" required placeholder="Liquid Emergency Fund">
								</div>
								<div class="wealthos-form-group">
									<label>Target Amount (${symbol})</label>
									<input type="number" id="sav-target" step="0.01" required placeholder="15000">
								</div>
								<div class="wealthos-form-group">
									<label>Current Amount (${symbol})</label>
									<input type="number" id="sav-current" step="0.01" required placeholder="3000">
								</div>
								<button type="submit" class="wealthos-btn">Save Goal</button>
							</form>
						</div>

						<div class="wealthos-card">
							<h3>Savings Goals Tracker</h3>
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

		// --- STAGE 8: INVESTMENTS MODULE ---
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
					<div class="wealthos-card" style="margin-bottom:20px; background:#eff6ff; border-left:4px solid #3b82f6;">
						<h4 style="margin:0 0 4px 0; color:#1e40af;">Stage 8: Long-Term Investing Goal</h4>
						<p style="margin:0; font-size:13px; color:#1e3a8a;">Build wealth consistently through broad-market index funds, ETFs, stocks, bonds, and retirement holdings.</p>
					</div>
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Add Investment Holding</h3>
							<form onsubmit="WealthOSApp.addInvestment(event)">
								<div class="wealthos-form-group">
									<label>Investment Name</label>
									<input type="text" id="inv-name" required placeholder="Vanguard Total Stock Market ETF (VTI)">
								</div>
								<div class="wealthos-form-group">
									<label>Asset Class</label>
									<select id="inv-class">
										<option value="Stocks">Stocks / Index ETFs</option>
										<option value="Bonds">Bonds</option>
										<option value="Real Estate">Real Estate REITs</option>
										<option value="Crypto">Crypto</option>
									</select>
								</div>
								<div class="wealthos-form-group">
									<label>Current Value (${symbol})</label>
									<input type="number" id="inv-value" step="0.01" required placeholder="25000">
								</div>
								<button type="submit" class="wealthos-btn">Save Investment</button>
							</form>
						</div>

						<div class="wealthos-card">
							<h3>Investment Portfolio Holdings</h3>
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

		// --- STAGE 9: PRODUCTIVE ASSETS MODULE ---
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
					<div class="wealthos-card" style="margin-bottom:20px; background:#eff6ff; border-left:4px solid #3b82f6;">
						<h4 style="margin:0 0 4px 0; color:#1e40af;">Stage 9: Productive Assets Goal</h4>
						<p style="margin:0; font-size:13px; color:#1e3a8a;">Acquire or build cash-flowing assets (rental properties, digital IP, equipment) that generate monthly income.</p>
					</div>
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Add Productive Asset</h3>
							<form onsubmit="WealthOSApp.addAsset(event)">
								<div class="wealthos-form-group">
									<label>Asset Title</label>
									<input type="text" id="ast-name" required placeholder="Duplex Rental Unit #1">
								</div>
								<div class="wealthos-form-group">
									<label>Category</label>
									<select id="ast-category">
										<option value="Real Estate">Real Estate</option>
										<option value="Business">Business Stream</option>
										<option value="Intellectual Property">Digital / IP Asset</option>
										<option value="Equipment">Equipment</option>
									</select>
								</div>
								<div class="wealthos-form-group">
									<label>Estimated Market Value (${symbol})</label>
									<input type="number" id="ast-val" step="0.01" required placeholder="320000">
								</div>
								<div class="wealthos-form-group">
									<label>Net Monthly Cash Flow / Income (${symbol})</label>
									<input type="number" id="ast-income" step="0.01" placeholder="850">
								</div>
								<button type="submit" class="wealthos-btn">Save Productive Asset</button>
							</form>
						</div>

						<div class="wealthos-card">
							<h3>Productive Assets Tracker</h3>
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

		// --- STAGE 10: BUSINESS MODULE ---
		loadBusiness: function(container) {
			const self = this;
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			this.apiFetch('business').then(data => {
				const m = data.metrics || {};
				container.innerHTML = `
					<div class="wealthos-card" style="margin-bottom:20px; background:#eff6ff; border-left:4px solid #3b82f6;">
						<h4 style="margin:0 0 4px 0; color:#1e40af;">Stage 10: Business Growth Goal</h4>
						<p style="margin:0; font-size:13px; color:#1e3a8a;">Track business revenue, expenses, operating profit, and owner draws to scale business cash reserves.</p>
					</div>
					<div class="wealthos-grid-2">
						<div class="wealthos-card">
							<h3>Log Monthly Business Metrics</h3>
							<form onsubmit="WealthOSApp.saveBusiness(event)">
								<div class="wealthos-form-group">
									<label>Monthly Gross Revenue (${symbol})</label>
									<input type="number" id="biz-rev" value="${m.revenue || ''}" required placeholder="15000">
								</div>
								<div class="wealthos-form-group">
									<label>Operating Expenses (${symbol})</label>
									<input type="number" id="biz-exp" value="${m.expenses || ''}" required placeholder="5000">
								</div>
								<div class="wealthos-form-group">
									<label>Owner Draw / Compensation (${symbol})</label>
									<input type="number" id="biz-draw" value="${m.owner_compensation || ''}" placeholder="6000">
								</div>
								<button type="submit" class="wealthos-btn">Update Business Metrics</button>
							</form>
						</div>

						<div class="wealthos-card">
							<h3>Business Financial Performance</h3>
							<div class="wealthos-grid-2">
								<div>
									<div class="wealthos-stat-sub">Monthly Revenue</div>
									<div class="wealthos-stat-value" style="font-size: 22px;">${symbol}${parseFloat(m.revenue || 0).toLocaleString()}</div>
								</div>
								<div>
									<div class="wealthos-stat-sub">Monthly Operating Profit</div>
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

		// --- STAGE 11: RISK MODULE ---
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
					<div class="wealthos-card" style="margin-bottom:20px; background:#eff6ff; border-left:4px solid #3b82f6;">
						<h4 style="margin:0 0 4px 0; color:#1e40af;">Stage 11: Risk Management Goal</h4>
						<p style="margin:0; font-size:13px; color:#1e3a8a;">Safeguard accumulated wealth against health, legal, cybersecurity, and emergency shocks.</p>
					</div>
					<div class="wealthos-card">
						<h3>Financial Risk Checklist</h3>
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

		// --- WEALTH ROADMAP ---
		loadRoadmap: function(container) {
			const stages = [
				{ step: 1, title: 'Know Your Numbers (Income)', desc: 'Log all income streams and pay cycles.' },
				{ step: 2, title: 'Calculate Cash Flow', desc: 'Ensure monthly income exceeds monthly expenses.' },
				{ step: 3, title: 'Control Expenses', desc: 'Plug discretionary spending leaks.' },
				{ step: 4, title: 'Proactive Budgeting', desc: 'Assign every dollar a job before spending.' },
				{ step: 5, title: 'Emergency Protection', desc: 'Build 3–6 months essential expense buffer.' },
				{ step: 6, title: 'Debt Freedom', desc: 'Eliminate credit cards via Avalanche or Snowball.' },
				{ step: 7, title: 'Milestone Savings', desc: 'Accumulate cash for medium-term goals.' },
				{ step: 8, title: 'Long-Term Investing', desc: 'Automate index fund and ETF contributions.' },
				{ step: 9, title: 'Productive Assets', desc: 'Acquire cash-flowing real estate or IP.' },
				{ step: 10, title: 'Business Growth', desc: 'Scale business revenue and profit draws.' },
				{ step: 11, title: 'Risk Management', desc: 'Complete 100% of risk protection checklist.' }
			];

			let items = stages.map(st => `
				<div style="display:flex; align-items:flex-start; gap:16px; margin-bottom:16px;">
					<div style="background:var(--wealthos-primary); color:#fff; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;">${st.step}</div>
					<div>
						<h4 style="margin:0 0 4px 0;">${st.title}</h4>
						<p style="margin:0; font-size:13px; color:var(--wealthos-text-muted);">${st.desc}</p>
					</div>
				</div>
			`).join('');

			container.innerHTML = `
				<div class="wealthos-card" style="max-width:700px; margin:0 auto;">
					<h3>11-Step Wealth Lifecycle Roadmap</h3>
					<p style="margin-bottom:20px;">Follow the 11 sequential business process stages to systematic wealth creation:</p>
					${items}
				</div>
			`;
		},

		// --- FINANCIAL INDEPENDENCE (FI) PROJECTIONS ---
		loadFI: function(container) {
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			container.innerHTML = `
				<div class="wealthos-card" style="max-width: 650px; margin: 0 auto;">
					<h3>Financial Independence (FI) Target Projections</h3>
					<p style="font-size:13px; color:var(--wealthos-text-muted);">
						The standard Financial Independence target uses the 25x Rule (Annual Expenses &times; 25).
					</p>

					<div class="wealthos-form-group">
						<label>Expected Annual Expenses (${symbol})</label>
						<input type="number" id="fi-expenses" value="48000">
					</div>
					<div class="wealthos-form-group">
						<label>Current Investments (${symbol})</label>
						<input type="number" id="fi-current" value="25000">
					</div>
					<div class="wealthos-form-group">
						<label>Monthly Contribution (${symbol})</label>
						<input type="number" id="fi-monthly" value="1000">
					</div>
					<div class="wealthos-form-group">
						<label>Assumed Real Annual Return (%)</label>
						<input type="number" id="fi-return" value="7.0" step="0.1">
					</div>
					<button class="wealthos-btn" onclick="WealthOSApp.runFICalc()">Calculate FI Projections</button>

					<div id="fi-results" style="margin-top: 20px; display: none;">
						<hr style="border: 0; border-top: 1px solid var(--wealthos-border); margin-bottom: 16px;">
						<div class="wealthos-grid-2">
							<div>
								<div class="wealthos-stat-sub">25x FI Target Number</div>
								<div class="wealthos-stat-value" id="fi-target-out" style="font-size: 22px;"></div>
							</div>
							<div>
								<div class="wealthos-stat-sub">Projected Portfolio (15 Yrs)</div>
								<div class="wealthos-stat-value" id="fi-projected-out" style="font-size: 22px; color: var(--wealthos-accent);"></div>
							</div>
						</div>
						<p class="wealthos-stat-sub" style="margin-top:12px; font-style:italic;">
							Illustration only. Actual investment returns, inflation, taxes, fees, and future expenses may differ substantially.
						</p>
					</div>
				</div>
			`;
		},

		runFICalc: function() {
			const exp = parseFloat(document.getElementById('fi-expenses').value) || 0;
			const cur = parseFloat(document.getElementById('fi-current').value) || 0;
			const m = parseFloat(document.getElementById('fi-monthly').value) || 0;
			const rate = parseFloat(document.getElementById('fi-return').value) || 0;
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			const target = exp * 25;
			const years = 15;
			const mRate = (rate / 100) / 12;
			const months = years * 12;

			let projected = cur * Math.pow(1 + mRate, months);
			if (mRate > 0) {
				projected += m * ((Math.pow(1 + mRate, months) - 1) / mRate);
			} else {
				projected += m * months;
			}

			document.getElementById('fi-target-out').innerText = symbol + Math.round(target).toLocaleString();
			document.getElementById('fi-projected-out').innerText = symbol + Math.round(projected).toLocaleString();
			document.getElementById('fi-results').style.display = 'block';
		},

		// --- SCENARIO PLANNER ("WHAT IF?") ---
		loadScenarioPlanner: function(container) {
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			container.innerHTML = `
				<div class="wealthos-card" style="max-width: 650px; margin: 0 auto;">
					<h3>"What-If?" Wealth Scenario Simulator</h3>
					<p style="font-size:13px; color:var(--wealthos-text-muted);">
						Model how small monthly habits compound into significant long-term net worth gains over 5 years.
					</p>

					<div class="wealthos-form-group">
						<label>Add Extra Savings / Investments (${symbol}/month)</label>
						<input type="number" id="sc-extra" value="300">
					</div>
					<div class="wealthos-form-group">
						<label>Increase Monthly Income (${symbol}/month)</label>
						<input type="number" id="sc-income" value="500">
					</div>
					<button class="wealthos-btn" onclick="WealthOSApp.runScenarioSim()">Run 5-Year Simulation</button>

					<div id="sc-results" style="margin-top: 20px; display: none;">
						<hr style="border: 0; border-top: 1px solid var(--wealthos-border); margin-bottom: 16px;">
						<div>
							<div class="wealthos-stat-sub">Estimated Additional 5-Year Wealth Created</div>
							<div class="wealthos-stat-value" id="sc-out" style="font-size: 26px; color: var(--wealthos-accent);"></div>
						</div>
					</div>
				</div>
			`;
		},

		runScenarioSim: function() {
			const extra = parseFloat(document.getElementById('sc-extra').value) || 0;
			const inc = parseFloat(document.getElementById('sc-income').value) || 0;
			const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

			const totalExtraM = extra + inc;
			const months = 60; // 5 years
			const mRate = 0.06 / 12; // 6% assumed annual return

			let additionalWealth = 0;
			if (mRate > 0) {
				additionalWealth = totalExtraM * ((Math.pow(1 + mRate, months) - 1) / mRate);
			} else {
				additionalWealth = totalExtraM * months;
			}

			document.getElementById('sc-out').innerText = symbol + Math.round(additionalWealth).toLocaleString();
			document.getElementById('sc-results').style.display = 'block';
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

		// --- REPORTS & EXPORTS MODULE ---
		loadReports: function(container) {
			const self = this;

			this.apiFetch('reports/annual').then(data => {
				const symbol = window.wealthosSettings ? window.wealthosSettings.currencySymbol : '$';

				container.innerHTML = `
					<div class="wealthos-card" style="max-width: 750px; margin: 0 auto;">
						<h3>Reports & Financial Exports</h3>
						<p style="color: var(--wealthos-text-muted);">
							Generate print-ready statement reports or export structured financial summary data.
						</p>

						<div class="wealthos-grid-2" style="margin-top: 20px;">
							<div class="wealthos-card" style="background:#f8fafc;">
								<h4 style="margin: 0 0 8px 0;">Annual Wealth Statement</h4>
								<p style="font-size: 13px; color: var(--wealthos-text-muted); margin-bottom: 16px;">
									Print or save a complete PDF report of income, expenses, net worth, and risk status.
								</p>
								<button class="wealthos-btn" onclick="window.print()">Print / Save PDF</button>
							</div>

							<div class="wealthos-card" style="background:#f8fafc;">
								<h4 style="margin: 0 0 8px 0;">Export Structured Data</h4>
								<p style="font-size: 13px; color: var(--wealthos-text-muted); margin-bottom: 16px;">
									Download a complete JSON record of your current financial system metrics.
								</p>
								<button class="wealthos-btn" style="background: var(--wealthos-primary);" onclick="WealthOSApp.exportJSONData()">Download JSON</button>
							</div>
						</div>

						<div style="margin-top:24px; padding-top:16px; border-top:1px solid var(--wealthos-border);">
							<h4>Annual Statement Summary Preview (${data.report_year})</h4>
							<table class="wealthos-table">
								<tr><td><strong>Annual Income</strong></td><td>${symbol}${parseFloat(data.annual_income).toLocaleString()}</td></tr>
								<tr><td><strong>Annual Expenses</strong></td><td>${symbol}${parseFloat(data.annual_expenses).toLocaleString()}</td></tr>
								<tr><td><strong>Annual Surplus</strong></td><td>${symbol}${parseFloat(data.annual_savings).toLocaleString()}</td></tr>
								<tr><td><strong>Net Worth Valuation</strong></td><td>${symbol}${parseFloat(data.net_worth).toLocaleString()}</td></tr>
							</table>
						</div>
					</div>
				`;
			});
		},

		exportJSONData: function() {
			this.apiFetch('dashboard-summary').then(data => {
				const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
				const url = URL.createObjectURL(blob);
				const a = document.createElement('a');
				a.href = url;
				a.download = 'wealthos-summary-export.json';
				a.click();
				URL.revokeObjectURL(url);
			});
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
