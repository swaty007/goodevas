export type ApiKey = {
    id: string | number;
    name: string;
    type: string;
    key: Record<string, string>;
    additional_data: Record<string, string>;
    created_at: string;
    updated_at: string;
};

export type ApiKeyForm = {
    name: string;
    type: string;
    key: Record<string, string>;
    additional_data: Record<string, string>;
};
