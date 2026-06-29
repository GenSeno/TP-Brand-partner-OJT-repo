<?php

namespace Database\Seeders;

use App\Models\Policy;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    public function run(): void
    {
        $policies = [
            [
                'slug' => 'terms',
                'title' => 'Terms and Conditions',
                'content' => '<h2>1. Acceptance of Terms</h2><p>By accessing or using our website and services, you agree to be bound by these Terms and Conditions. If you do not agree, please do not use our site or services.</p><h2>2. Definitions</h2><p>"We," "Us," "Our" refers to TP Ink Lab / Tribu Pakaras. "You," "Your" refers to the user, visitor, or customer accessing our platform.</p><h2>3. Account Registration</h2><p>You may be required to create an account to access certain features. You are responsible for maintaining the confidentiality of your login credentials and for all activities under your account.</p><h2>4. Products and Orders</h2><p>All product descriptions, prices, and availability are subject to change without notice. We reserve the right to refuse or cancel any order at our discretion.</p><h2>5. Intellectual Property</h2><p>All content on this website — including text, images, logos, and designs — is the property of TP Ink Lab unless otherwise stated. Unauthorized use is prohibited.</p><h2>6. Limitation of Liability</h2><p>We shall not be liable for any indirect, incidental, or consequential damages arising from the use of our products or website.</p><h2>7. Governing Law</h2><p>These terms are governed by the laws of the Republic of the Philippines.</p><h2>8. Changes to Terms</h2><p>We may update these terms at any time. Continued use after changes constitutes acceptance of the updated terms.</p>',
                'last_updated_date' => 'June 16, 2026',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'privacy',
                'title' => 'Privacy Policy',
                'content' => '<h2>1. Information We Collect</h2><p>We collect personal information you provide directly, such as your name, email address, shipping address, and payment details when you make a purchase or create an account.</p><h2>2. How We Use Your Information</h2><p>We use your information to process orders, communicate with you, improve our services, and comply with legal obligations.</p><h2>3. Data Sharing</h2><p>We do not sell your personal information. We may share data with trusted third-party service providers (payment processors, shipping carriers) strictly for order fulfillment.</p><h2>4. Data Security</h2><p>We implement reasonable security measures to protect your data. However, no method of transmission over the internet is 100% secure.</p><h2>5. Your Rights</h2><p>You have the right to access, correct, or delete your personal data. You may also withdraw consent at any time by contacting us.</p><h2>6. Contact</h2><p>For privacy-related inquiries, contact us at tribupakarasph@gmail.com.</p>',
                'last_updated_date' => 'June 16, 2026',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'refund',
                'title' => 'Refund and Return Policy',
                'content' => '<h2>1. Eligibility</h2><p>Items may be returned within 30 days of delivery. To be eligible, products must be unworn, unwashed, and in their original packaging with all tags attached.</p><h2>2. Non-Returnable Items</h2><p>Sale items, personalized/custom products, and underwear/swimwear for hygiene reasons are final sale and cannot be returned.</p><h2>3. Return Process</h2><p>Contact us at tribupakarasph@gmail.com to initiate a return. We will provide a return authorization and shipping instructions. Return shipping costs are the customer\'s responsibility unless the item is defective.</p><h2>4. Refunds</h2><p>Once we receive and inspect your return, we will notify you of the approval or rejection. Approved refunds will be processed to the original payment method within 5-10 business days.</p><h2>5. Exchanges</h2><p>We offer exchanges for size or color subject to availability. Contact us to arrange an exchange.</p>',
                'last_updated_date' => 'June 16, 2026',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'slug' => 'shipping',
                'title' => 'Shipping and Delivery Policy',
                'content' => '<h2>Our Shipping Policy</h2><h3>Processing Time</h3><p>Orders are processed within 1-3 business days after payment confirmation.</p><h3>Delivery Time</h3><p>Metro Manila: 2-4 business days<br>Provincial Areas: 4-7 business days<br>International: 7-14 business days</p><h3>Shipping Rates</h3><p>Shipping fees are calculated at checkout based on your location and order weight. Free shipping is available for orders above a certain threshold as indicated on our site.</p><h3>Tracking</h3><p>A tracking number will be provided once your order is shipped. You can track your package through the carrier\'s website.</p><hr><h2>Third-Party Carrier Terms</h2><p>We ship through third-party carriers. Delivery estimates are provided by the carrier and are not guaranteed. Claims for lost, damaged, or delayed packages must be filed directly with the carrier per their terms.</p><ul><li><strong>LBC Express</strong> — <a href="https://www.lbcexpress.com/terms" target="_blank" rel="noopener">LBC Terms of Service</a></li><li><strong>J&T Express</strong> — <a href="https://www.jtexpress.ph/terms" target="_blank" rel="noopener">J&T Terms of Service</a></li><li><strong>Ninja Van</strong> — <a href="https://www.ninjavan.ph/terms" target="_blank" rel="noopener">Ninja Van Terms</a></li></ul><p>For shipping-related concerns, please contact us before filing a claim with the carrier so we can assist.</p>',
                'last_updated_date' => 'June 16, 2026',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'slug' => 'payment',
                'title' => 'Payment Policy',
                'content' => '<h2>Our Payment Policy</h2><h3>Accepted Payment Methods</h3><p>We accept the following payment methods:</p><ul><li>Credit/Debit Cards (Visa, Mastercard, JCB)</li><li>GCash</li><li>PayMaya</li><li>Bank Transfers</li><li>Cash on Delivery (select areas)</li></ul><h3>Currency</h3><p>All transactions are processed in Philippine Pesos (PHP).</p><h3>Payment Authorization</h3><p>Your payment will be authorized at the time of purchase and captured upon order confirmation. For Cash on Delivery, payment is collected upon delivery.</p><h3>Invoices</h3><p>An invoice will be sent to your email after payment is confirmed. Please retain this for your records.</p><hr><h2>Third-Party Payment Gateway Terms</h2><p>We process payments through secure third-party gateways. By completing a purchase, you also agree to the respective gateway\'s terms and conditions. TP Ink Lab does not store full payment card details.</p><ul><li><strong>Xendit</strong> — <a href="https://www.xendit.co/terms" target="_blank" rel="noopener">Xendit Terms of Service</a></li><li><strong>PayMongo</strong> — <a href="https://paymongo.com/terms" target="_blank" rel="noopener">PayMongo Terms of Service</a></li></ul>',
                'last_updated_date' => 'June 16, 2026',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'slug' => 'cookies',
                'title' => 'Cookie Policy',
                'content' => '<h2>1. What Are Cookies</h2><p>Cookies are small text files stored on your device when you visit a website. They help us improve your browsing experience by remembering your preferences and site behavior.</p><h2>2. Types of Cookies We Use</h2><ul><li><strong>Essential Cookies:</strong> Required for the website to function (e.g., session management, shopping cart).</li><li><strong>Analytics Cookies:</strong> Help us understand how visitors interact with our site (e.g., page views, traffic sources).</li><li><strong>Functional Cookies:</strong> Remember your preferences for a personalized experience.</li><li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements and measure campaign effectiveness.</li></ul><h2>3. Managing Cookies</h2><p>You can control cookie preferences through our cookie consent banner or by adjusting your browser settings. Disabling certain cookies may affect site functionality.</p><h2>4. Third-Party Cookies</h2><p>We may use services like Google Analytics and Facebook Pixel, which set their own cookies. These are governed by the respective third-party privacy policies.</p><h2>5. Updates</h2><p>We may update this Cookie Policy from time to time. Changes will be posted on this page.</p>',
                'last_updated_date' => 'June 16, 2026',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'slug' => 'warranty',
                'title' => 'Warranty Policy',
                'content' => '<h2>1. Warranty Coverage</h2><p>We stand behind the quality of our products. All apparel and accessories purchased from Tribu Pakaras are covered by a 30-day warranty against manufacturing defects.</p><h2>2. What Is Covered</h2><ul><li>Seam or stitching defects</li><li>Material defects (fabric flaws, holes)</li><li>Zipper or hardware malfunction</li></ul><h2>3. What Is Not Covered</h2><ul><li>Normal wear and tear</li><li>Damage caused by misuse, improper washing, or alteration</li><li>Products purchased more than 30 days ago</li></ul><h2>4. Warranty Claim Process</h2><p>Contact us at tribupakarasph@gmail.com with your order number, photos of the defect, and a description of the issue. We will review and provide instructions for replacement or repair.</p><h2>5. Resolution</h2><p>Depending on availability, we will either repair, replace, or issue a store credit for the defective item.</p>',
                'last_updated_date' => 'June 16, 2026',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'slug' => 'cancellation',
                'title' => 'Cancellation Policy',
                'content' => '<h2>1. Order Cancellation</h2><p>You may cancel your order within 24 hours of placing it, provided it has not yet been shipped. To cancel, please contact us immediately at tribupakarasph@gmail.com.</p><h2>2. Cancellation After Shipping</h2><p>Once an order has been shipped, it cannot be canceled. Please refer to our Refund and Return Policy for returning items after delivery.</p><h2>3. Cancellation by Us</h2><p>We reserve the right to cancel orders in cases of suspected fraud, inaccurate pricing, or stock unavailability. You will be notified and fully refunded.</p><h2>4. Refunds for Canceled Orders</h2><p>Refunds for canceled orders will be processed to the original payment method within 5-10 business days.</p>',
                'last_updated_date' => 'June 16, 2026',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'slug' => 'disclaimer',
                'title' => 'Disclaimer',
                'content' => '<h2>1. General Information</h2><p>The content on this website is provided for general informational purposes only. It is not intended as professional advice and should not be relied upon as such.</p><h2>2. Product Use</h2><p>Our products are designed for their intended use (e.g., running, training, lifestyle). We make no claims regarding medical or performance benefits. Always consult a professional before starting any fitness regimen.</p><h2>3. Accuracy of Content</h2><p>While we strive for accuracy, we do not warrant that product descriptions, images, pricing, or availability are error-free. We reserve the right to correct errors without prior notice.</p><h2>4. External Links</h2><p>Our site may contain links to third-party websites. We are not responsible for the content or practices of these external sites.</p><h2>5. Limitation of Liability</h2><p>TP Ink Lab / Tribu Pakaras shall not be held liable for any damages arising from the use or inability to use our products or website.</p>',
                'last_updated_date' => 'June 16, 2026',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'slug' => 'acceptable-use',
                'title' => 'Acceptable Use Policy',
                'content' => '<h2>1. Prohibited Activities</h2><p>You agree not to use our website or services for any unlawful, harmful, or abusive purposes, including but not limited to:</p><ul><li>Violating any applicable laws or regulations</li><li>Impersonating any person or entity</li><li>Attempting to gain unauthorized access to our systems</li><li>Uploading malicious code or interfering with site operations</li><li>Engaging in fraudulent or deceptive activities</li><li>Harassing, threatening, or abusing others</li></ul><h2>2. User Content</h2><p>Any content you submit (reviews, comments, etc.) must not infringe on third-party rights or contain offensive material. We reserve the right to remove content at our discretion.</p><h2>3. Account Security</h2><p>You are responsible for maintaining the security of your account. Notify us immediately of any unauthorized use.</p><h2>4. Enforcement</h2><p>Violation of this policy may result in account suspension, termination, and legal action where applicable.</p>',
                'last_updated_date' => 'June 16, 2026',
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($policies as $data) {
            Policy::updateOrCreate(
                ['slug' => $data['slug']],
                $data,
            );
        }
    }
}
