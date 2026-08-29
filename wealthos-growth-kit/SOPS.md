# WealthOS — Standard Operating Procedures (SOPs)
## Operations, Support Desk, Plugin Release QA, and Affiliate Management

---

### SOP 1: Customer Onboarding & License Key Activation

#### Objective
Ensure a 100% friction-free setup experience for new WealthOS customers.

#### Procedure
1. **Automated Post-Purchase Email**:
   - Customer completes checkout on WooCommerce/Stripe.
   - System triggers instant email with download link for `wealthos.zip` and license key activation code.
2. **WordPress Plugin Installation**:
   - Customer navigates to `WordPress Admin -> Plugins -> Add New -> Upload Plugin`.
   - Selects `wealthos.zip` and clicks **Activate**.
3. **Shortcode Placement**:
   - Customer creates or edits a page (e.g. `yourdomain.com/wealth-dashboard/`).
   - Inserts `[wealthos_dashboard]` shortcode block.
4. **Onboarding Setup Wizard**:
   - On first visit to the page, customer completes the 5-field Onboarding Setup Wizard (Age Range, Currency, Employment Type, Risk Tolerance, Primary Goal).

---

### SOP 2: Technical Support Desk & SLA Guidelines

#### Objective
Deliver world-class technical support with a $<4$ hour response time SLA.

#### Support Triage Classification
* **Priority 1 (Critical)**: Plugin activation error, REST API blocked, database table delta fail.
  - *SLA*: Response within 1 hour; resolution within 4 hours.
* **Priority 2 (General Query)**: Questions regarding Debt Avalanche calculation math or currency symbol overrides.
  - *SLA*: Response within 4 hours.
* **Priority 3 (Feature Request)**: Requests for custom asset classes or new export formats.
  - *SLA*: Logged in product roadmap backlog within 24 hours.

---

### SOP 3: Quality Assurance (QA) & Plugin Release Deployment

#### Objective
Guarantee zero syntax or mathematical calculation bugs prior to every public update.

#### Pre-Release Checklist
1. **Run Automated Test Runner**:
   - Execute `php tests/run-tests.php` in terminal.
   - Verify all test suites (Cash Flow, Compounding, FI 25x Target, Debt Avalanche, Net Worth Growth) pass with 0 errors.
2. **PHP Syntax Check**:
   - Execute `php -l` on all modified files across `includes/`, `admin/`, `public/`, and `templates/`.
3. **Database Migration Verification**:
   - Test activation on clean WordPress installation to verify `dbDelta()` table creation.
4. **Version Bump**:
   - Update `WEALTHOS_VERSION` constant in `wealthos.php` and `readme.txt`.

---

### SOP 4: Affiliate Partner Program Management

#### Objective
Recruit, onboard, and support high-performing financial blogger and agency affiliates.

#### Procedure
1. **Affiliate Recruitment Criteria**:
   - Personal finance bloggers, FIRE content creators, WordPress agency owners, and certified financial coaches.
2. **Commission Structure**:
   - 30% recurring payout on annual licenses ($23.70/yr per Personal sale; $59.70/yr per Agency sale).
   - 40% payout on Lifetime Founder Deals ($119.60 per sale).
3. **Asset Provisioning**:
   - Provide affiliate swipe emails from `MARKETING.md`, video script templates, and banner graphics upon approval.
