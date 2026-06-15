# How to Add Multiple Product Images

## Step 1: Update Your Database

1. **Run the install.php** to create the new `product_images` table:
   - Visit: `http://localhost/project/install.php`
   - This will create the `product_images` table that stores multiple images per product

2. **Or manually add the table** using your database client:
```sql
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_image (product_id, image_path)
);
```

## Step 2: Add Images for Products

### Method 1: Using Admin Panel (If Available)
- Upload multiple images when creating/editing a product
- Images will be stored in the `uploads/` folder
- Each image will be added to the `product_images` table

### Method 2: Direct Database Insert
```sql
INSERT INTO product_images (product_id, image_path, alt_text, sort_order) VALUES
(1, 'uploads/product-red.jpg', 'Product in Red', 1),
(1, 'uploads/product-blue.jpg', 'Product in Blue', 2),
(1, 'uploads/product-green.jpg', 'Product in Green', 3);
```

### Method 3: Manual Upload & Database Entry
1. Upload images to the `uploads/` folder
2. Insert into database:
```sql
INSERT INTO product_images (product_id, image_path, alt_text, sort_order) 
VALUES (5, 'uploads/shirt-xl.jpg', 'Shirt XL Size', 1);
```

## Step 3: Upload Images

1. Create/Upload images to: `uploads/` folder
   - Use formats: JPG, PNG, WebP
   - Recommended size: 800x800px or larger
   - Keep file names descriptive

2. Example files:
   - `uploads/product-1-color-red.jpg`
   - `uploads/product-1-color-blue.jpg`
   - `uploads/product-1-color-black.jpg`

## Features

✅ **Multiple Images** - Show different colors, angles, or styles
✅ **Click to View** - Customers can click thumbnails to view full images
✅ **Sortable** - Control image order using `sort_order` field
✅ **Alt Text** - Add descriptions for each image (SEO friendly)
✅ **Fallback** - If no product_images exist, uses the main product image

## Example Usage

### Product with Different Colors
- Product: "T-Shirt"
- Images:
  - Red version (sort_order: 1)
  - Blue version (sort_order: 2)
  - Black version (sort_order: 3)

### Product with Multiple Angles
- Product: "Phone"
- Images:
  - Front view (sort_order: 1)
  - Back view (sort_order: 2)
  - Side view (sort_order: 3)
  - Camera detail (sort_order: 4)

## Database Schema

```
product_images table:
- id: Unique identifier
- product_id: Links to products table
- image_path: Full path to image (e.g., 'uploads/image.jpg')
- alt_text: Description (optional, for SEO)
- sort_order: Display order (1, 2, 3, etc.)
- created_at: When image was added
```

## Troubleshooting

**Q: Images not showing?**
A: Make sure the image path in database matches the actual file location in `uploads/` folder

**Q: Old single image still showing?**
A: The system first checks `product_images` table. If empty, it falls back to the main product image.

**Q: How to change image order?**
A: Update the `sort_order` field:
```sql
UPDATE product_images SET sort_order = 2 WHERE id = 5;
UPDATE product_images SET sort_order = 1 WHERE id = 3;
```

**Q: Delete an image?**
A: Remove from database:
```sql
DELETE FROM product_images WHERE id = 5;
```
