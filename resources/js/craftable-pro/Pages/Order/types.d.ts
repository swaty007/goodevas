export type OrderItem = {
    id: string | number;
    order_id: string;
    item_id: string;
    api_order_id: string;
    quantity: number;
    title: string;
    sku: string;
}

export type Order = {
    id: string | number;
    order_id: string;
    type: string;
    order_date: string;
    update_date: string;
    order_status: string;
    fulfillment: string;
    sales_channel: string;
    total_amount: number;
    total_currency: string;
    payment_method: string;
    buyer_name: string;
    address_line_1: string;
    address_line_2: string;
    city: string;
    state: string;
    postal_code: string;
    country_code: string;
    expected_ship_date: string;
    is_shipped: boolean;
    original_object: Record<string, string>;
    created_at: string;
    updated_at: string;
    items: OrderItem[];
};

export type OrderForm = {
    order_id: string;
    type: string;
    order_date: string;
    update_date: string;
    order_status: string;
    fulfillment: string;
    sales_channel: string;
    total_amount: number;
    total_currency: string;
    payment_method: string;
    buyer_name: string;
    address_line_1: string;
    address_line_2: string;
    city: string;
    state: string;
    postal_code: string;
    country_code: string;
    expected_ship_date: string;
    is_shipped: boolean;
};
