

export type Product = {
    id: string | number; 
ext_id: varchar; 
ean: varchar; 
additional_data: string; 
product_type_id: integer; 
created_at: string; 
updated_at: string
    
};

export type ProductForm = {
    ext_id: varchar; 
ean: varchar; 
additional_data: string; 
product_type_id: integer
};
