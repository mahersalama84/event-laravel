declare namespace App.Enums {
    export type ActiveStatus = 1 | 0;
    export type PublishStatus = 0 | 1;
}

export type CreateCustomerData = {
    id: any | any | string;
    is_active: App.Enums.ActiveStatus | any;
    first_name: string;
    last_name: string;
    mobile: string;
    password: string | null;
    image: any | null;
    mobile_verified_at: string | null;
};
export type CustomerData = {
    id: any | any | string;
    is_active: App.Enums.ActiveStatus | any;
    full_name: string;
    first_name: string;
    last_name: string;
    mobile: string;
    image: string | null;
    mobile_verified_at: string | null;
};
export type UpdateCustomerData = {
    id: any | any | string;
    is_active: App.Enums.ActiveStatus | any;
    first_name: string;
    last_name: string;
    mobile: string;
    password: string | null;
    image: any | null;
    mobile_verified_at: string | null;
};
