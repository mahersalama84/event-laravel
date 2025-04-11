declare namespace App.Enums {
    export type ActiveStatus = 1 | 0;
    export type PublishStatus = 0 | 1;
}

export type AdvertisementData = {
    id: any | any | string;
    published: App.Enums.PublishStatus | any;
    image: any;
};
