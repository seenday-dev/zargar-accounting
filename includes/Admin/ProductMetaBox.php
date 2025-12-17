<?php
/**
 * Product Meta Box Handler
 * 
 * Displays and saves Zargar accounting fields in product edit page
 * 
 * @package ZargarAccounting
 * @since 2.0.0
 */

namespace ZargarAccounting\Admin;

use ZargarAccounting\Logger\MonologManager;

if (!defined('ABSPATH')) {
    exit;
}

class ProductMetaBox {
    private static $instance = null;
    private $logger;
    
    /**
     * Meta fields configuration
     */
    private $meta_fields = [
        [
            'id' => '_location',
            'label' => 'محل نگهداری',
            'type' => 'text',
            'placeholder' => 'مثال: انبار اصلی'
        ],
        [
            'id' => '_external_id',
            'label' => 'شناسه خارجی',
            'type' => 'text',
            'placeholder' => 'کد محصول در سیستم زرگر'
        ],
        [
            'id' => '_stone_price',
            'label' => 'قیمت سنگ',
            'type' => 'number',
            'placeholder' => '0',
            'step' => '0.01'
        ],
        [
            'id' => 'wage_price',
            'label' => 'قیمت اجرت',
            'type' => 'number',
            'placeholder' => '0',
            'step' => '0.01'
        ],
        [
            'id' => '_income_total',
            'label' => 'جمع درآمد',
            'type' => 'number',
            'placeholder' => '0',
            'step' => '0.01',
            'readonly' => true
        ],
        [
            'id' => '_tax_total',
            'label' => 'جمع مالیات',
            'type' => 'number',
            'placeholder' => '0',
            'step' => '0.01',
            'readonly' => true
        ],
        [
            'id' => 'sale_wage_percent',
            'label' => 'درصد اجرت فروش',
            'type' => 'number',
            'placeholder' => '0',
            'step' => '0.01',
            'min' => '0',
            'max' => '100'
        ],
        [
            'id' => 'sale_wage_price',
            'label' => 'مبلغ اجرت فروش',
            'type' => 'number',
            'placeholder' => '0',
            'step' => '0.01'
        ],
        [
            'id' => 'sale_wage_price_type',
            'label' => 'نوع اجرت فروش',
            'type' => 'select',
            'options' => [
                '' => 'انتخاب کنید',
                'fixed' => 'مبلغ ثابت',
                'percent' => 'درصدی'
            ]
        ],
        [
            'id' => 'sale_wage_stone',
            'label' => 'اجرت سنگ فروش',
            'type' => 'number',
            'placeholder' => '0',
            'step' => '0.01'
        ],
        [
            'id' => '_office_code',
            'label' => 'کد دفتر',
            'type' => 'text',
            'placeholder' => 'کد دفتر'
        ],
        [
            'id' => '_designer_code',
            'label' => 'کد طراح',
            'type' => 'text',
            'placeholder' => 'کد طراح'
        ],
        [
            'id' => '_extra_field_1',
            'label' => 'فیلد اضافه ۱',
            'type' => 'text',
            'placeholder' => 'اطلاعات اضافی'
        ],
        [
            'id' => '_extra_field_2',
            'label' => 'فیلد اضافه ۲',
            'type' => 'text',
            'placeholder' => 'اطلاعات اضافی'
        ]
    ];
    
    private function __construct() {
        $this->logger = MonologManager::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function registerHooks(): void {
        add_action('add_meta_boxes', [$this, 'addMetaBox']);
        add_action('save_post_product', [$this, 'saveMetaBox'], 10, 2);
    }
    
    /**
     * Add meta box to product edit page
     */
    public function addMetaBox(): void {
        add_meta_box(
            'zargar_accounting_meta_box',
            '<span class="dashicons dashicons-calculator" style="margin-left: 5px;"></span> متاباکس زرگر',
            [$this, 'renderMetaBox'],
            'product',
            'normal',
            'high'
        );
    }
    
    /**
     * Render meta box content
     */
    public function renderMetaBox($post): void {
        // Add nonce for security
        wp_nonce_field('zargar_meta_box_nonce', 'zargar_meta_box_nonce');
        
        // Get data from database
        $repository = \ZargarAccounting\Database\ProductRepository::getInstance();
        $zargar_data = $repository->getByProductId($post->ID);
        
        ?>
        <style>
            .zargar-meta-box {
                padding: 12px;
            }
            .zargar-meta-box-header {
                background: linear-gradient(135deg, #D4AF37 0%, #F4E5C3 100%);
                padding: 15px 20px;
                margin: -12px -12px 20px -12px;
                border-radius: 4px 4px 0 0;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .zargar-meta-box-header .dashicons {
                font-size: 24px;
                width: 24px;
                height: 24px;
                color: #fff;
            }
            .zargar-meta-box-header h3 {
                margin: 0;
                color: #fff;
                font-size: 16px;
                font-weight: 600;
            }
            .zargar-meta-fields {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            .zargar-field {
                display: flex;
                flex-direction: column;
            }
            .zargar-field label {
                font-weight: 600;
                margin-bottom: 6px;
                color: #1e1e1e;
                font-size: 13px;
            }
            .zargar-field input[type="text"],
            .zargar-field input[type="number"],
            .zargar-field select,
            .zargar-field textarea {
                padding: 8px 12px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
                transition: all 0.3s ease;
            }
            .zargar-field input[type="text"]:focus,
            .zargar-field input[type="number"]:focus,
            .zargar-field select:focus,
            .zargar-field textarea:focus {
                border-color: #D4AF37;
                outline: none;
                box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
            }
            .zargar-field input[readonly] {
                background-color: #f5f5f5;
                cursor: not-allowed;
            }
            .zargar-field-description {
                font-size: 12px;
                color: #666;
                margin-top: 4px;
                font-style: italic;
            }
            .zargar-field-readonly {
                background-color: #f9f9f9;
                border: 1px dashed #ddd;
                padding: 10px;
                border-radius: 4px;
            }
            .zargar-sync-info {
                background: #e7f3ff;
                border: 1px solid #b3d9ff;
                border-radius: 4px;
                padding: 12px 15px;
                margin-top: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .zargar-sync-info .dashicons {
                color: #0073aa;
                font-size: 20px;
                width: 20px;
                height: 20px;
            }
            .zargar-sync-info p {
                margin: 0;
                color: #0073aa;
                font-size: 13px;
            }
            @media (max-width: 782px) {
                .zargar-meta-fields {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        
        <div class="zargar-meta-box">
            <div class="zargar-meta-box-header">
                <span class="dashicons dashicons-calculator"></span>
                <h3>اطلاعات حسابداری زرگر</h3>
            </div>
            
            <div class="zargar-meta-fields">
                <?php foreach ($this->meta_fields as $field): ?>
                    <div class="zargar-field">
                        <label for="<?php echo esc_attr($field['id']); ?>">
                            <?php echo esc_html($field['label']); ?>
                        </label>
                        
                        <?php
                        // Get value from database or fallback to postmeta
                        $field_key = str_replace('_', '', $field['id']);
                        $value = $zargar_data[$field_key] ?? get_post_meta($post->ID, $field['id'], true);
                        $readonly = isset($field['readonly']) && $field['readonly'];
                        
                        switch ($field['type']):
                            case 'select':
                                ?>
                                <select 
                                    name="<?php echo esc_attr($field['id']); ?>" 
                                    id="<?php echo esc_attr($field['id']); ?>"
                                    <?php echo $readonly ? 'disabled' : ''; ?>>
                                    <?php foreach ($field['options'] as $opt_value => $opt_label): ?>
                                        <option value="<?php echo esc_attr($opt_value); ?>" <?php selected($value, $opt_value); ?>>
                                            <?php echo esc_html($opt_label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php
                                break;
                                
                            case 'textarea':
                                ?>
                                <textarea 
                                    name="<?php echo esc_attr($field['id']); ?>" 
                                    id="<?php echo esc_attr($field['id']); ?>"
                                    rows="3"
                                    placeholder="<?php echo esc_attr($field['placeholder'] ?? ''); ?>"
                                    <?php echo $readonly ? 'readonly' : ''; ?>><?php echo esc_textarea($value); ?></textarea>
                                <?php
                                break;
                                
                            default: // text, number
                                ?>
                                <input 
                                    type="<?php echo esc_attr($field['type']); ?>" 
                                    name="<?php echo esc_attr($field['id']); ?>" 
                                    id="<?php echo esc_attr($field['id']); ?>"
                                    value="<?php echo esc_attr($value); ?>"
                                    placeholder="<?php echo esc_attr($field['placeholder'] ?? ''); ?>"
                                    <?php if (isset($field['step'])): ?>step="<?php echo esc_attr($field['step']); ?>"<?php endif; ?>
                                    <?php if (isset($field['min'])): ?>min="<?php echo esc_attr($field['min']); ?>"<?php endif; ?>
                                    <?php if (isset($field['max'])): ?>max="<?php echo esc_attr($field['max']); ?>"<?php endif; ?>
                                    <?php echo $readonly ? 'readonly' : ''; ?>>
                                <?php
                                break;
                        endswitch;
                        ?>
                        
                        <?php if ($readonly): ?>
                            <span class="zargar-field-description">
                                این فیلد به صورت خودکار محاسبه می‌شود
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="zargar-sync-info">
                <span class="dashicons dashicons-update"></span>
                <p>این اطلاعات با سیستم حسابداری زرگر همگام‌سازی می‌شوند. تغییرات به صورت خودکار در سرور مرکزی ثبت خواهد شد.</p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save meta box data
     */
    public function saveMetaBox(int $post_id, $post): void {
        // Check nonce
        if (!isset($_POST['zargar_meta_box_nonce']) || !wp_verify_nonce($_POST['zargar_meta_box_nonce'], 'zargar_meta_box_nonce')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Check if it's a revision
        if (wp_is_post_revision($post_id)) {
            return;
        }
        
        $save_data = [];
        $changes = [];
        
        // Collect data from fields
        foreach ($this->meta_fields as $field) {
            // Skip readonly fields (they are calculated)
            if (isset($field['readonly']) && $field['readonly']) {
                continue;
            }
            
            $field_id = $field['id'];
            $field_key = str_replace('_', '', $field_id); // Remove underscore for DB column
            
            if (isset($_POST[$field_id])) {
                $new_value = $this->sanitizeFieldValue($_POST[$field_id], $field['type']);
                $save_data[$field_key] = $new_value;
            } else {
                $save_data[$field_key] = null;
            }
        }
        
        // Calculate totals
        $repository = \ZargarAccounting\Database\ProductRepository::getInstance();
        $totals = $repository->calculateTotals($save_data);
        $save_data['income_total'] = $totals['income_total'];
        $save_data['tax_total'] = $totals['tax_total'];
        
        // Save to database
        $saved = $repository->save($post_id, $save_data);
        
        if ($saved) {
            $this->logger->product('متادیتای محصول بروزرسانی شد', [
                'product_id' => $post_id,
                'product_title' => get_the_title($post_id),
                'data' => $save_data,
                'user_id' => get_current_user_id()
            ]);
        } else {
            $this->logger->error('خطا در ذخیره متادیتای محصول', [
                'product_id' => $post_id
            ]);
        }
    }
    
    /**
     * Sanitize field value based on type
     */
    private function sanitizeFieldValue($value, string $type) {
        switch ($type) {
            case 'number':
                return is_numeric($value) ? floatval($value) : '';
                
            case 'email':
                return sanitize_email($value);
                
            case 'url':
                return esc_url_raw($value);
                
            case 'textarea':
                return sanitize_textarea_field($value);
                
            default: // text, select
                return sanitize_text_field($value);
        }
    }
    
    /**
     * Get meta field value
     */
    public function getMetaValue(int $post_id, string $meta_key) {
        return get_post_meta($post_id, $meta_key, true);
    }
    
    /**
     * Update meta field value
     */
    public function updateMetaValue(int $post_id, string $meta_key, $meta_value): bool {
        return update_post_meta($post_id, $meta_key, $meta_value);
    }
}
