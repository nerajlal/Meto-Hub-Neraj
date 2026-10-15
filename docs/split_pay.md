# MetoHub: subscriptions, seller payments and automatic settlements

**Planning document — 15 October 2026**  
**Scope:** India, INR payments, small Instagram sellers creating independent stores on MetoHub. This document proposes a future payment model; it does not implement or enable payments.

## 1. Is the idea possible?

**Yes, subject to payment-provider approval and seller verification.** MetoHub can provide one integrated checkout experience and arrange automatic settlements to sellers through an approved marketplace payment product. Sellers would not necessarily need to integrate their own standalone gateway or supply API keys.

The distinction is that **“no separate gateway integration” does not mean “no seller onboarding or KYC.”** Each seller must be identified, approved for the relevant arrangement, and mapped to a verified settlement destination.

The proposed model is:

- Sellers pay MetoHub a recurring subscription for the software.
- Buyers pay for products through the platform’s approved payment integration.
- The payment provider allocates the sale proceeds to the correct seller.
- The provider settles to the seller’s verified bank account or supported UPI ID.
- MetoHub receives an optional agreed transaction commission separately from its subscription revenue.

The preferred approach is provider-managed collection and settlement. Do not assume an ordinary gateway account permits collecting unrelated sellers’ sales into MetoHub’s operating account and forwarding the money later.

## 2. Keep the two payment flows separate

| Flow | Who pays? | Who receives the money? | Suitable product |
| --- | --- | --- | --- |
| SaaS subscription | Seller/store owner | MetoHub | Subscription billing or recurring mandates |
| Product purchase | Buyer | Seller, with any agreed platform share | Marketplace collection and split settlement |

### Monthly SaaS subscription

A seller authorizes a subscription mandate for the selected plan. Scheduled charges pay for MetoHub’s services. A seller who makes no sales can still owe the monthly subscription.

Razorpay documents plan-based recurring billing, subscription notifications and UPI AutoPay support. A mandate requires authorization; knowing a seller’s UPI ID does not authorize automatic debits. Actual supported methods, limits and account activation must be confirmed. [Razorpay Subscriptions](https://razorpay.com/docs/payments/subscriptions/)

**Proposed billing behavior:** show the amount and renewal terms, collect consent, record subscription status, provide cancellation, and handle unsuccessful renewals with reminders and a defined grace period. Reconcile later renewal events as well as the first payment.

### Buyer payments and seller settlements

A buyer pays for a specific store’s order. The provider associates that collection with the approved seller and settles according to the contracted schedule. A successful buyer payment and a successful seller settlement are separate events.

Even if both flows use the same provider, maintain separate records for subscriptions, orders, fees, refunds and settlements. Do not count the seller’s gross sales as MetoHub’s SaaS revenue.

## 3. What “send it to their UPI number” means

The seller should supply a **UPI ID/VPA**, such as `seller@bank`, rather than an unverified mobile number. A mobile number alone is not a dependable settlement identifier. An approved provider may support another verified identifier, but that must be explicit.

A UPI settlement ultimately credits the bank account associated with that VPA. It is not a MetoHub wallet balance.

Buyer payment methods and seller settlement methods are independent: a buyer might pay by card while an eligible seller receives a settlement through UPI. Availability depends on the provider’s approved configuration.

**Proposed seller experience:** “Connect payouts” → submit required business/identity information → verify the bank account or UPI ID → receive approval → enable online checkout.

Keep a verified bank destination as a fallback where the provider supports it. Changing a destination should require fresh verification and an audit trail, rather than immediately redirecting existing settlements.

## 4. Providers worth evaluating

### Razorpay Route

Route is a marketplace-oriented product for allocating collections to linked accounts and managing the subsequent money movement. It is a separate capability from basic payment acceptance. [Razorpay Route](https://razorpay.com/docs/payments/route/)

Current documentation describes linked-account onboarding, business details and settlement destination verification. Do not assume a seller is eligible merely because an account-creation request succeeds. [Razorpay Linked Accounts](https://razorpay.com/docs/payments/route/linked-account/)

Razorpay also documents **VPA settlements**. The feature requires an active Route account, separate enablement and verification/name matching. The published page lists ₹1 lakh per transaction and per beneficiary VPA per day. It also describes separate linked accounts when bank and VPA destinations are both needed. Confirm eligibility, current limits and suitability for Instagram retail sellers in writing; these are not guarantees for MetoHub’s account. [Razorpay VPA Settlements](https://razorpay.com/docs/payments/route/linked-account/vpa-settlements)

**Why investigate it:** the project already contains Razorpay subscription integration code. That may reduce provider fragmentation, but it does not establish Route approval or production readiness.

### Cashfree Easy Split

Easy Split provides vendor management, payment allocation, settlement and reconciliation capabilities. It is a relevant alternative for evaluating the complete marketplace flow. [Cashfree Easy Split API overview](https://www.cashfree.com/docs/api-reference/payments/latest/split/easy-split-overview)

Cashfree’s official vendor-request model includes bank details, UPI details and KYC information. Its marketplace explanation describes bank and UPI settlement options subject to configuration and eligibility. The presence of these fields is not proof that every seller or account qualifies. [Official Cashfree vendor model](https://github.com/cashfree/cashfree-pg/blob/main/model_create_vendor_request.go), [Cashfree marketplace settlement explanation](https://www.cashfree.com/blog/marketplace-payment-splitting-in-india/)

**Why investigate it:** compare the actual approval and onboarding experience for very small sellers, UPI destination support, refund recovery and total cost.

### Decision rule

Choose the provider that approves MetoHub’s exact business and seller profile with workable onboarding and settlement terms. Do not choose purely from advertised features or a successful sandbox payment. No final provider selection or pricing commitment is made in this document.

## 5. Important approval question for small Instagram sellers

India’s payment-aggregator framework covers authorization, merchant due diligence and escrow operations. The RBI’s 2025 Directions require checks around merchant onboarding and destination validation. They also impose conditions on certain settlements to third parties, including turnover-based eligibility. That third-party provision should not be confused with every seller’s own merchant settlement. [RBI Payment Aggregator Directions, 2025](https://www.rbi.org.in/Scripts/BS_ViewMasDirections.aspx?id=12896)

**Implication for this project:** establish whether each Instagram seller will be treated as an onboarded merchant/sub-merchant or as a third-party beneficiary. Ask the provider how its approved structure accommodates sellers below ₹40 lakh annual turnover. Do not assume a generic “split payment” API bypasses these distinctions.

The intended design is for an authorized provider to manage regulated collection and settlement. This is not a conclusion that MetoHub automatically has no regulatory obligations. Obtain written provider approval and advice on the actual contracts and money flow before launch.

## 6. Proposed order flow

```text
Seller pays monthly subscription ──> MetoHub subscription revenue

Buyer opens one seller's store
        |
        v
MetoHub creates an order for that verified seller
        |
        v
Buyer pays through the approved provider checkout
        |
        v
Server verifies payment using provider events/API
        |
        v
Order marked paid; seller share and fees recorded
        |
        v
Provider settles seller share ──> verified bank account / eligible UPI ID
        |
        +──> any agreed platform share settles to MetoHub
```

Start with **one seller per checkout**. MetoHub hosts multiple independent stores; that does not require introducing a cross-store cart. Reject a mismatch between the store, order and seller settlement account.

## 7. Subscription-only versus transaction commissions

| Commercial model | Seller billing | Sale allocation |
| --- | --- | --- |
| Subscription only | Monthly software fee | Seller receives sale proceeds after agreed deductions |
| Commission only | Per completed transaction | Platform retains an agreed share |
| Hybrid | Monthly fee plus transaction commission | Both charges must be disclosed separately |

**Proposed starting point:** retain clear monthly subscription billing. Add a transaction commission only after deciding whether it improves the business model and getting provider approval for the allocation.

Do not silently recover overdue subscriptions from sales. Such recovery would need explicit seller agreement, provider support, clear accounting and review of refund/tax consequences. A failed subscription should not silently confiscate seller funds or remove access to historical payment and dispute records.

### Illustration, not a provider quote

For a ₹1,000 order with a hypothetical 2% MetoHub commission:

- Buyer pays ₹1,000.
- Platform commission is ₹20 before any separately applicable tax.
- Seller allocation is ₹980 **before** gateway fees, settlement charges, taxes, withholding or other agreed adjustments.
- The monthly subscription is billed separately.

₹980 is not a promised final bank credit. Define who bears every charge and how rounding, shipping, discounts and partial refunds affect the calculation.

## 8. Seller onboarding information

The final requirements must come from the selected provider. Plan for:

- Legal name, store name, contact details and address.
- Seller type, product category and expected transaction activity.
- PAN or other identification permitted by the approved onboarding process.
- Business documents and GST information where applicable.
- Verified bank account or eligible UPI ID, with beneficiary-name checks.
- Acceptance of payout, refund, chargeback and commission terms.
- Provider approval status and reasons when more information is required.

Do not promise that incorporation or GST registration is always mandatory, or that neither is needed. Eligibility differs by entity, category and circumstances. Specifically ask about individuals, sole proprietors, home businesses and sellers without GST registration.

Prefer provider-hosted onboarding/document collection where available. MetoHub should retain only the sensitive information it needs, with restricted access and a retention policy. Never collect UPI PINs, OTPs, banking passwords or full card credentials.

## 9. Automation and accounting requirements for a future build

These are design requirements, not implemented features:

- Verify payment signatures/events server-side and check order amount, currency and tenant. A browser success screen is insufficient.
- Process duplicate and delayed events safely. Use idempotency for transfers, refunds and payouts; reconcile an unknown result before retrying it.
- Track separate payment, allocation, settlement, refund and dispute states. “Paid” must not mean “settled.”
- Maintain an auditable ledger for gross collection, provider charges, platform share, seller payable, refunds and settlement references.
- Reconcile the ledger against provider reports and bank credits; alert on missing events, failed settlements and unexplained differences.
- Prevent seller-to-seller data access. Restrict destination changes and privileged financial actions.
- Use queues and reconciliation jobs for failures. Do not trigger a fresh seller transfer simply because a buyer refreshes a page.
- Keep credentials server-side, use hosted checkout, and test provider downtime and settlement failures.

The seller dashboard should distinguish sales received, deductions, pending settlement, completed settlement, refunds and amounts under dispute. Show the actual provider settlement reference when available.

## 10. Refunds, chargebacks and settlement delays

Automatic distribution does not remove responsibility for returns or disputes. Decide before launch:

- Who approves a refund and who funds it after the seller has already been paid?
- Can the provider reverse a transfer or offset future settlements? What happens with insufficient funds or no future sales?
- Which fees and platform commission are refundable?
- How are partial refunds allocated?
- Who handles dispute deadlines and evidence?
- What happens when a seller closes their store or changes payout details?

Refunds should use the approved provider refund process against the original payment. Sending a separate UPI transfer to a buyer is not equivalent to reversing that payment.

Do not promise universal instant settlement. Timing can depend on approval, provider schedules, limits, risk review and banking availability. A provider-supported hold or reserve policy must be transparent and contractually permitted; it is not permission for MetoHub to hold funds indefinitely.

## 11. Costs, tax and seller-facing promises

Request a complete written quote covering collection fees, subscription/mandate charges, split transfers, UPI/bank settlement charges, verification, refunds, disputes, minimum commitments and taxes. “UPI supported” does not mean the complete service is free.

Have an Indian accountant review the proposed structure for invoicing, GST, any applicable e-commerce tax collection or withholding, seller statements and treatment of provider fees. Applicability depends on the arrangement; this document does not prescribe tax rates or a universal tax outcome.

Suggested seller-facing wording, once approved:

> Accept online payments without integrating your own gateway. Complete payment verification, connect an approved payout destination, and receive settlements according to the displayed schedule.

Avoid promising “UPI number only, no verification,” guaranteed instant payouts, zero fees or automatic approval for every seller.

## 12. Current project versus future scope

A read-only review of this workspace found:

- Razorpay subscription-related code in `app/Http/Controllers/Admin/BillingController.php`.
- Provider configuration in `config/services.php`.
- `app/Services/CheckoutPaymentPolicy.php` currently permits COD when enabled and explicitly does not enable online methods without a verified payment flow.

These observations do not verify live gateway activation. Seller onboarding, approved split collections, settlement accounting and payout reconciliation are separate future work. This document does not enable online checkout, alter billing, create provider accounts or change the database.

## 13. Questions to send to providers before implementation

1. Will you approve a SaaS platform hosting independent Instagram stores, including small individuals and sole proprietors?
2. Is each seller an onboarded merchant/sub-merchant or a third-party beneficiary? How do current RBI requirements affect low-turnover sellers?
3. Can sellers join through our platform without standalone gateway accounts or API keys?
4. What exact identity, business and bank/UPI verification is required? Can the provider host that flow?
5. Are retail sellers eligible for VPA settlements on our account? What limits, name checks and bank fallbacks apply?
6. Can monthly SaaS subscription revenue and seller sales be supported with distinct accounting under the proposed account setup?
7. How are fees, platform commissions, taxes, refunds and chargebacks allocated?
8. What are the actual settlement schedule, reserve/hold rules, failure recovery and seller closure procedures?
9. Which events and reports support payment-to-settlement reconciliation? What are production access and support requirements?
10. What are all costs, minimum volumes and contractual liabilities?

## 14. Proposed rollout

1. Obtain written approval for the exact business and seller profile; select a provider and confirm the legal/tax structure.
2. Define transparent subscription, commission, refund and settlement terms.
3. Prove seller onboarding, buyer payment, settlement and refund in the provider’s test environment.
4. Validate a controlled live pilot with a small set of approved sellers, including failed and post-settlement refund cases.
5. Expand only after reconciliation and support processes work reliably.

**Decision:** pursue provider-managed marketplace payments with separate SaaS subscriptions. Treat seller verification and provider approval as prerequisites, and treat UPI settlement as an eligible destination option rather than a substitute for onboarding.
