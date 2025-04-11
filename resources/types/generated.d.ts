declare namespace App.Data {
export type AdvertisementData = {
id: any | any | string;
published: App.Enums.PublishStatus | any;
image: any;
};
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
export type ImageData = {
image: any;
};
export type OccasionData = {
id: any | any | string;
customer_id: string | null;
description: string | null;
title: string;
start_date: string;
start_time: string;
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
export type WishData = {
id: any | any | string;
description: string | null;
title: string;
occasion_id: string;
image: any | null;
};
}
declare namespace App.Enums {
export type ActiveStatus = 1 | 0;
export type PublishStatus = 0 | 1;
}
