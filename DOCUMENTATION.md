# WealthOS — Personal Wealth Operating System
## Complete Business Process Manual, Module Guides & Form Instructions

---

### Master Wealth Lifecycle Business Process Flow

WealthOS is architected around a strict, interconnected 11-stage wealth creation lifecycle:

$$\text{1. Income} \rightarrow \text{2. Cash Flow} \rightarrow \text{3. Expense Control} \rightarrow \text{4. Emergency Fund} \rightarrow \text{5. Debt Reduction}$$
$$\rightarrow \text{6. Savings} \rightarrow \text{7. Investing} \rightarrow \text{8. Asset Building} \rightarrow \text{9. Business Growth} \rightarrow \text{10. Risk Management} \rightarrow \text{11. Net Worth Growth}$$

Every form, input field, and calculation in WealthOS feeds directly into this sequential engine.

---

### Table of Contents (By Business Process Stage)

1. **System Onboarding & Profiling**
2. **Stage 1: Income Module & Growth Planner**
3. **Stage 2: Cash Flow Engine & Surplus Dashboard**
4. **Stage 3: Expense Control Module & Leak Detector**
5. **Stage 4: Budget Module (Zero-Based / Custom)**
6. **Stage 5: Emergency Reserve Planner**
7. **Stage 6: Debt Reduction System (Avalanche vs. Snowball)**
8. **Stage 7: Savings Goals Tracker**
9. **Stage 8: Investment Portfolio Tracker & Allocation**
10. **Stage 9: Productive Asset Building Module**
11. **Stage 10: Entrepreneur & Business Growth Module**
12. **Stage 11: Risk Management Checklist & Score**
13. **Central Intelligence: Net Worth Engine & WealthScore (0–100)**
14. **Central Intelligence: Bottleneck Engine & Action Center ("What Should I Do Next?")**
15. **Central Intelligence: Visual Wealth Roadmap**
16. **Projections & Calculators: Compounding Calculator & FI Projections**
17. **Projections & Calculators: Scenario Planner ("What If?")**
18. **Admin White-Labeling & System Configuration**

---

### 1. System Onboarding & Profiling

#### Business Process Position & Goal
The entry point of the WealthOS operating system. The goal is to establish baseline demographic context, currency preferences, and primary goals without collecting sensitive or PII data.

#### Form Input Guide & Examples
* **Age Range**: Dropdown (`18-24`, `25-34`, `35-44`, `45-54`, `55+`). *Example*: Select `25-34`.
* **Currency Symbol**: Text input for global currency display. *Example*: Enter `$` (or `€`, `£`, `¥`).
* **Employment Type**: Dropdown (`Employed / Salaried`, `Freelancer / Contractor`, `Entrepreneur / Business Owner`, `Other`). *Example*: Select `Employed / Salaried`.
* **Risk Tolerance**: Dropdown (`Conservative`, `Moderate`, `Aggressive`). *Example*: Select `Moderate`.
* **Primary Financial Goal**: Text input defining top milestone. *Example*: Enter `Build $100k Net Worth & Eliminate Credit Debt`.

#### What to Accomplish / Goal
Initializes user profile state in `wealthos_user_profile` table and unlocks the dashboard.

---

### 2. Stage 1: Income Module & Growth Planner

#### Business Process Position & Goal
**Stage 1 of 11**. Establishing and expanding earning power. The goal is to record all revenue sources, convert diverse pay cycles to monthly metrics, and identify income concentration risks.

#### Form Input Guide & Examples
* **Income Stream Name**: Text field. *Example*: `Senior Software Engineer Salary`.
* **Category**: Dropdown (`Salary`, `Freelance`, `Business`, `Investments`, `Side Hustle`, `Other`). *Example*: Select `Salary`.
* **Amount**: Number field in local currency. *Example*: `5500.00`.
* **Frequency**: Dropdown (`Monthly`, `Annually`, `Weekly`, `Biweekly`, `Quarterly`). *Example*: Select `Monthly`.

#### System Calculations & Outcomes
* **Monthly Normalization**: Converts any frequency into monthly baseline:
  $$\text{Annual } (\$66,000) \div 12 = \$5,500/\text{month}$$
* **Income Concentration Alert**: Triggered if a single source represents $>90\%$ of total earnings.
* **Goal Achieved**: Complete clarity on gross monthly earning power.

---

### 3. Stage 2: Cash Flow Engine & Surplus Dashboard

#### Business Process Position & Goal
**Stage 2 of 11**. Calculating net financial surplus. The goal is to ensure Total Monthly Income exceeds Total Monthly Expenses, creating the capital required to fund all downstream stages (Emergency, Debt, Investing).

#### System Calculations & Outcomes
* **Cash Flow Formula**:
  $$\text{Cash Flow Surplus} = \text{Total Monthly Income} - \text{Total Monthly Expenses}$$
* **Savings Rate Formula**:
  $$\text{Savings Rate \%} = \left( \frac{\text{Cash Flow Surplus}}{\text{Total Monthly Income}} \right) \times 100\%$$
* **Goal Achieved**: Generating a positive savings rate ($\ge 15–20\%$).

---

### 4. Stage 3: Expense Control Module & Leak Detector

#### Business Process Position & Goal
**Stage 3 of 11**. Plug expense leaks and optimize spending ratios. The goal is to categorize expenses into Essential vs. Discretionary needs and eliminate wasteful recurring subscriptions.

#### Form Input Guide & Examples
* **Expense Name**: Text field. *Example*: `Apartment Rent`.
* **Category**: Dropdown (`Housing`, `Food`, `Utilities`, `Transportation`, `Subscriptions`, `Entertainment`, `Other`). *Example*: `Housing`.
* **Amount**: Number field. *Example*: `1400.00`.
* **Type**: Dropdown (`Essential` or `Discretionary`). *Example*: `Essential`.

#### System Calculations & Outcomes
* **Expense Leak Detection**: Flags discretionary spending exceeding $45\%$ of total expenses or $\ge 3$ active subscriptions.
* **Goal Achieved**: Minimizing non-essential leaks without non-judgmental shaming.

---

### 5. Stage 4: Budget Module (Zero-Based / Custom)

#### Business Process Position & Goal
**Stage 4 of 11**. Proactive capital allocation. The goal is to assign every dollar of surplus a job before the month begins.

#### Form Input Guide & Examples
* **Category Target**: Text field. *Example*: `Groceries`.
* **Allocated Monthly Amount**: Number field. *Example*: `500.00`.

#### System Calculations & Outcomes
* **Budget vs. Actual Variance**:
  $$\text{Variance} = \text{Allocated Target} - \text{Actual Expenses}$$
  * *Example*: Target $\$500$, Actual $\$450 \rightarrow +\$50$ (Under Budget / Healthy).
* **Goal Achieved**: Zero unallocated dollars; total spending matches plan.

---

### 6. Stage 5: Emergency Reserve Planner

#### Business Process Position & Goal
**Stage 5 of 11**. Financial defense and risk protection. The goal is to build a liquid emergency buffer to avoid taking on new debt during unexpected life shocks.

#### Form Input Guide & Examples
* **Goal Name**: Text field. *Example*: `Liquid Emergency Fund`.
* **Target Amount**: Number field. *Example*: `15000.00` (6 months of $\$2,500$ essential expenses).
* **Current Amount**: Number field. *Example*: `3000.00`.

#### System Calculations & Outcomes
* **Stage Progress**:
  * *Stage 1*: Starter Reserve ($\$1,000$).
  * *Stage 2*: 1 Month Essential Expenses.
  * *Stage 3*: 3 Months Essential Expenses.
  * *Stage 4*: 6 Months Essential Expenses (Fully Funded).
* **Goal Achieved**: 100% completion of Stage 4 reserve.

---

### 7. Stage 6: Debt Reduction System (Avalanche vs. Snowball)

#### Business Process Position & Goal
**Stage 6 of 11**. High-interest debt elimination. The goal is to systematically pay off liabilities using mathematical (Avalanche) or behavioral (Snowball) strategies.

#### Form Input Guide & Examples
* **Debt Name**: Text field. *Example*: `Rewards Credit Card`.
* **Current Balance**: Number field. *Example*: `4500.00`.
* **Interest Rate (%)**: Number field. *Example*: `21.5`.
* **Minimum Payment**: Number field. *Example*: `120.00`.

#### System Calculations & Outcomes
* **Debt Avalanche**: Prioritizes highest interest rate first to minimize interest cost.
* **Debt Snowball**: Prioritizes smallest balance first for rapid psychological wins.
* **Goal Achieved**: Total elimination of high-interest consumer debt ($>8–10\%$).

---

### 8. Stage 7: Savings Goals Tracker

#### Business Process Position & Goal
**Stage 7 of 11**. Capital accumulation for specific medium-term milestones (house down payment, vehicle, business capital).

#### Form Input Guide & Examples
* **Goal Title**: Text field. *Example*: `Home Down Payment`.
* **Target Amount**: Number field. *Example*: `30000.00`.
* **Current Saved**: Number field. *Example*: `12000.00`.
* **Deadline**: Date field (`YYYY-MM-DD`). *Example*: `2025-12-31`.

#### System Calculations & Outcomes
* **Required Monthly Contribution**:
  $$\text{Required Contribution} = \frac{\text{Target Amount} - \text{Current Amount}}{\text{Months Remaining to Deadline}}$$
* **Goal Achieved**: Meeting required monthly contribution rate to hit milestone on schedule.

---

### 9. Stage 8: Investment Portfolio Tracker & Allocation

#### Business Process Position & Goal
**Stage 8 of 11**. Long-term wealth compounding. The goal is to track asset accumulation in index funds, stocks, bonds, and retirement accounts.

#### Form Input Guide & Examples
* **Asset Name**: Text field. *Example*: `Vanguard S&P 500 ETF (VOO)`.
* **Asset Class**: Dropdown (`Stocks`, `Bonds`, `Real Estate`, `Crypto`, `Cash`). *Example*: `Stocks`.
* **Current Market Value**: Number field. *Example*: `35000.00`.

#### System Calculations & Outcomes
* **Asset Allocation Donut SVG**: Calculates asset class percentages. Flags concentration risks ($\ge 75\%$).
* **Goal Achieved**: Building a diversified, growing investment portfolio.

---

### 10. Stage 9: Productive Asset Building Module

#### Business Process Position & Goal
**Stage 9 of 11**. Cash-flowing asset accumulation. The goal is to acquire or build assets that generate passive income (rental real estate, businesses, digital IP, equipment).

#### Form Input Guide & Examples
* **Asset Title**: Text field. *Example*: `Duplex Rental Unit #1`.
* **Estimated Value**: Number field. *Example*: `320000.00`.
* **Monthly Cash Flow / Income**: Number field. *Example*: `850.00`.

#### System Calculations & Outcomes
* **Net Cash Flow Contribution**: Adds net asset income directly to Stage 1 Monthly Income.
* **Goal Achieved**: Replacing active job earnings with productive asset cash flow.

---

### 11. Stage 10: Entrepreneur & Business Growth Module

#### Business Process Position & Goal
**Stage 10 of 11**. Business scaling for entrepreneurs and freelancers. The goal is to track revenue, expenses, operating profit, and owner compensation.

#### Form Input Guide & Examples
* **Monthly Revenue**: Number field. *Example*: `15000.00`.
* **Operating Expenses**: Number field. *Example*: `5000.00`.
* **Owner Draw / Compensation**: Number field. *Example*: `6000.00`.

#### System Calculations & Outcomes
* **Operating Profit**:
  $$\text{Operating Profit} = \text{Revenue} - \text{Operating Expenses} = \$10,000/\text{month}$$
* **Retained Reserve**: $\text{Operating Profit} - \text{Owner Compensation} = \$4,000$.
* **Goal Achieved**: Reinvesting profit reserves to scale revenue.

---

### 12. Stage 11: Risk Management Checklist & Score

#### Business Process Position & Goal
**Stage 11 of 11**. Wealth protection and risk mitigation. The goal is to safeguard wealth against legal, medical, cybersecurity, or dependency shocks.

#### Form Input Guide & Examples
* **Checklist Items**: Toggle dropdown (`Needs Attention` or `Complete`) across Health Insurance, Life Insurance, Emergency Reserve, Debt Elimination, 2FA/Password Security, and Beneficiary Updates.

#### System Calculations & Outcomes
* **Risk Score (0–100)**: Evaluates complete items. Low score indicates high protection.
* **Goal Achieved**: 100% completion of risk checklist.

---

### 13. Central Intelligence: Net Worth Engine & WealthScore (0–100)

#### Business Process Position & Goal
Central financial scoreboard evaluating overall progress.

#### Formulas & Logic
$$\text{Net Worth} = \text{Total Assets} - \text{Total Liabilities}$$
$$\text{WealthScore (0–100)} = \text{Cash Flow (20)} + \text{Emergency (20)} + \text{Debt (20)} + \text{Investing (20)} + \text{Risk (20)}$$

---

### 14. Central Intelligence: Bottleneck Engine & Action Center

#### Goal
Automated identification of the single highest-priority obstacle holding back your net worth growth, generating prioritized action cards.

---

### 15. Projections & Calculators: Compounding & FI (25x Rule)

#### Goal
Interactive compounding growth calculator and Financial Independence (FI) 25x Annual Expense target calculator.

$$\text{FI Target} = \text{Annual Expenses} \times 25$$

---

### 16. Admin White-Labeling & System Configuration
Located under `WordPress Admin -> WealthOS -> Settings`:
* **App Name, Tagline, Primary Color, Accent Color, Currency Symbol, and Uninstall Data Deletion Toggle**.
