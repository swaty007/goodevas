export type StockChange = {
    total_consumption: {
        key: number;
    };
    total_stock: {
        key: number;
    };
};

export type Product = {
    id: string | number;
    ext_id: string;
    ean: string;
    additional_data: string;
    product_type_id: number;
    created_at: string;
    updated_at: string
    stock_changes: StockChange
};

export type ProductForm = {
    ext_id: string;
    ean: string;
    additional_data: string;
    product_type_id: number
};
