export type WarehouseSettings = {
    ranges: Array<{
        min: number;
        max: number;
        color: string;
    }>;
};

export type Warehouse = {
    id: string | number;
    name: string;
    ysell_name: string;
    created_at: string;
    updated_at: string;
    country_id: varchar;
    settings: WarehouseSettings;
    futureIncomesDates: Array<string>;
};

export type WarehouseForm = {
    name: string;
    country_id: string;
    hidden: string;
    settings: WarehouseSettings;
};
