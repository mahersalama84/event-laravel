type Form = {
    search: string;
    role: string;
    is_active: boolean;
};
type Sorting = {
    sortBy: string;
    sortType: string;
};
type Pagination = {
    page: number;
    per_page: number;
};
export type { Form, Sorting, Pagination };
