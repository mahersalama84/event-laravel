<script setup>
import { watch, ref, nextTick } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, usePage, Head } from "@inertiajs/vue3";
import Chart from "@/Charts/Chart.vue";
import { useDark } from "@vueuse/core";
import DatePicker from "@/Shared/DatePicker.vue";
import DateFilter from "@/Shared/DateFilter.vue";

const user = usePage().props.auth.user;
const charts_trans = usePage().props.language.charts;

const isDark = useDark();
const props = defineProps([
    "UserStats",
    "CustomerStats",
    "start",
    "end",
    "duration",
]);
const duration = ref(props.duration);
const rendered = ref(true);
watch(
    () => duration.value,
    (newData, oldData) => {
        router.get(
            route("dashboard", {
                start: newData,
                end: null,
            })
        );
    }
);
watch(
    () => isDark.value,
    (newData, oldData) => {
        if (newData) {
            UsersChartOptions.plugins.title.color = "#ffffff";
            UsersChartOptions.plugins.legend.labels.color = "#ffffff";
            UsersChartOptions.scales.x.ticks.color = "#ffffff";
            UsersChartOptions.scales.y.ticks.color = "#ffffff";
            UsersChartOptions.scales.x.border.color = "#666666";
            UsersChartOptions.scales.y.border.color = "#666666";
            UsersChartOptions.scales.x.grid.color = "#666666";
            UsersChartOptions.scales.y.grid.color = "#666666";

            CustomersChartOptions.plugins.title.color = "#ffffff";
            CustomersChartOptions.plugins.legend.labels.color = "#ffffff";
            CustomersChartOptions.scales.x.ticks.color = "#ffffff";
            CustomersChartOptions.scales.y.ticks.color = "#ffffff";
            CustomersChartOptions.scales.x.border.color = "#666666";
            CustomersChartOptions.scales.y.border.color = "#666666";
            CustomersChartOptions.scales.x.grid.color = "#666666";
            CustomersChartOptions.scales.y.grid.color = "#666666";
        } else {
            UsersChartOptions.plugins.title.color = "#000000";
            UsersChartOptions.plugins.legend.labels.color = "#000000";
            UsersChartOptions.scales.x.ticks.color = "#000000";
            UsersChartOptions.scales.y.ticks.color = "#000000";
            UsersChartOptions.scales.x.border.color = "#D3D3D3";
            UsersChartOptions.scales.y.border.color = "#D3D3D3";
            UsersChartOptions.scales.x.grid.color = "#D3D3D3";
            UsersChartOptions.scales.y.grid.color = "#D3D3D3";

            CustomersChartOptions.plugins.title.color = "#000000";
            CustomersChartOptions.plugins.legend.labels.color = "#000000";
            CustomersChartOptions.scales.x.ticks.color = "#000000";
            CustomersChartOptions.scales.y.ticks.color = "#000000";
            CustomersChartOptions.scales.x.border.color = "#D3D3D3";
            CustomersChartOptions.scales.y.border.color = "#D3D3D3";
            CustomersChartOptions.scales.x.grid.color = "#D3D3D3";
            CustomersChartOptions.scales.y.grid.color = "#D3D3D3";
        }
        rendered.value = false;
        nextTick(() => {
            rendered.value = true;
        });
    }
);

const reset = () => {
    router.get(
        route("dashboard", {
            start: null,
            end: null,
        })
    );
};
const UserChartLabels = props.UserStats?.map((stat) => stat.start);
const UserChartTitle = "Users";
const UserChartValues = props.UserStats?.map((stat) => stat.value);
const UserChartIncrements = props.UserStats?.map((stat) => stat.increments);
const UserChartDeccrements = props.UserStats?.map((stat) => stat.decrements);
const UserChartDifference = props.UserStats?.map((stat) => stat.difference);
let UserData = {
    labels: UserChartLabels,
    datasets: [
        {
            label: charts_trans["increments"],
            fill: false,
            backgroundColor: ["#41B883"],
            borderColor: "rgba(179,181,198,1)",
            pointBackgroundColor: "rgba(179,181,198,1)",
            pointBorderColor: "#fff",
            pointHoverBackgroundColor: "#fff",
            pointHoverBorderColor: "rgba(179,181,198,1)",
            data: UserChartIncrements,
        },
        {
            label: charts_trans["decrements"],
            fill: false,
            backgroundColor: ["#DD1B16"],
            borderColor: "rgba(179,181,198,1)",
            pointBackgroundColor: "rgba(179,181,198,1)",
            pointBorderColor: "#fff",
            pointHoverBackgroundColor: "#fff",
            pointHoverBorderColor: "rgba(179,181,198,1)",
            data: UserChartDeccrements,
        },
        {
            label: charts_trans["difference"],
            fill: false,
            backgroundColor: ["#00D8FF"],
            borderColor: "rgba(179,181,198,1)",
            pointBackgroundColor: "rgba(179,181,198,1)",
            pointBorderColor: "#fff",
            pointHoverBackgroundColor: "#fff",
            pointHoverBorderColor: "rgba(179,181,198,1)",
            data: UserChartDifference,
        },
    ],
};

const CustromerChartLabels = props.CustomerStats?.map((stat) => stat.start);
const CustromerChartTitle = "Customers";
const CustomerChartValues = props.CustomerStats?.map((stat) => stat.value);
const CustomerChartIncrements = props.CustomerStats?.map(
    (stat) => stat.increments
);
const CustomerChartDeccrements = props.CustomerStats?.map(
    (stat) => stat.decrements
);
const CustomerChartDifference = props.CustomerStats?.map(
    (stat) => stat.difference
);

let CustomerData = {
    labels: CustromerChartLabels,
    datasets: [
        {
            label: charts_trans["increments"],
            fill: false,
            backgroundColor: ["#41B883"],
            borderColor: "rgba(179,181,198,1)",
            pointBackgroundColor: "rgba(179,181,198,1)",
            pointBorderColor: "#fff",
            pointHoverBackgroundColor: "#fff",
            pointHoverBorderColor: "rgba(179,181,198,1)",
            data: CustomerChartIncrements,
        },
        {
            label: charts_trans["decrements"],
            fill: false,
            backgroundColor: ["#DD1B16"],
            borderColor: "rgba(179,181,198,1)",
            pointBackgroundColor: "rgba(179,181,198,1)",
            pointBorderColor: "#fff",
            pointHoverBackgroundColor: "#fff",
            pointHoverBorderColor: "rgba(179,181,198,1)",
            data: CustomerChartDeccrements,
        },
        {
            label: charts_trans["difference"],
            fill: false,
            backgroundColor: ["#00D8FF"],
            borderColor: "rgba(179,181,198,1)",
            pointBackgroundColor: "rgba(179,181,198,1)",
            pointBorderColor: "#fff",
            pointHoverBackgroundColor: "#fff",
            pointHoverBorderColor: "rgba(179,181,198,1)",
            data: CustomerChartDifference,
        },
    ],
};

const UsersChartOptions = {
    type: Object,
    aspectRatio: 1,
    animation: false,
    animations: {
        tension: {
            duration: 1000,
            easing: "linear",
            from: 1,
            to: 0,
            loop: true,
        },
    },
    scales: {
        y: {
            // min: 0,
            // max: 100,
            display: true,
            border: {
                color: isDark.value ? "#666666" : "#D3D3D3",
            },
            grid: {
                color: isDark.value ? "#666666" : "#D3D3D3",
            },
            ticks: {
                callback: (value) => `${value} U`,
                color: isDark.value ? "#ffffff" : "#000000",
            },
        },
        x: {
            display: true,
            border: {
                color: isDark.value ? "#666666" : "#D3D3D3",
            },
            grid: {
                color: isDark.value ? "#666666" : "#D3D3D3",
            },
            ticks: {
                color: isDark.value ? "#ffffff" : "#000000",
            },
        },
    },
    plugins: {
        title: {
            display: true,
            text: charts_trans["users"],
            color: isDark.value ? "#ffffff" : "#000000",
        },
        legend: {
            display: true,
            labels: {
                color: isDark.value ? "#ffffff" : "#000000",
            },
        },
        tooltip: {
            enabled: true,
            callbacks: {
                // labelColor: function (context) {
                //     return {
                //         borderColor: "rgb(0, 0, 255)",
                //         backgroundColor: "rgb(255, 0, 0)",
                //         borderWidth: 2,
                //         borderDash: [2, 2],
                //         borderRadius: 2,
                //     };
                // },
                labelTextColor: function (context) {
                    return "#ffffff";
                },
            },
        },
    },
    default: {
        responsive: true,
        maintainAspectRatio: true,
    },
};
const CustomersChartOptions = {
    type: Object,
    aspectRatio: 1,
    animation: false,
    animations: {
        tension: {
            duration: 1000,
            easing: "linear",
            from: 1,
            to: 0,
            loop: true,
        },
    },
    scales: {
        y: {
            // min: 0,
            // max: 100,
            display: true,
            border: {
                color: isDark.value ? "#666666" : "#D3D3D3",
            },
            grid: {
                color: isDark.value ? "#666666" : "#D3D3D3",
            },
            ticks: {
                callback: (value) => `${value} C`,
                color: isDark.value ? "#ffffff" : "#000000",
            },
        },
        x: {
            display: true,
            border: {
                color: isDark.value ? "#666666" : "#D3D3D3",
            },
            grid: {
                color: isDark.value ? "#666666" : "#D3D3D3",
            },
            ticks: {
                color: isDark.value ? "#ffffff" : "#000000",
            },
        },
    },
    plugins: {
        title: {
            display: true,
            text: charts_trans["customers"],
            color: isDark.value ? "#ffffff" : "#000000",
        },
        legend: {
            display: true,
            labels: {
                color: isDark.value ? "#ffffff" : "#000000",
            },
        },
        tooltip: {
            enabled: true,
            callbacks: {
                // labelColor: function (context) {
                //     return {
                //         borderColor: "rgb(0, 0, 255)",
                //         backgroundColor: "rgb(255, 0, 0)",
                //         borderWidth: 2,
                //         borderDash: [2, 2],
                //         borderRadius: 2,
                //     };
                // },
                labelTextColor: function (context) {
                    return "#ffffff";
                },
            },
        },
    },
    default: {
        responsive: true,
        maintainAspectRatio: true,
    },
};
</script>

<template>
    <Head :title="__('dashboard')['dashboard']" />
    <AuthenticatedLayout>
        <template #header>
            {{ __("dashboard")["dashboard"] }}
        </template>
        <div class="filter-container">
            <date-filter
                :start="start"
                :end="end"
                class="filter-main"
                @reset="reset"
            >
                <label class="block text-gray-700 dark:text-white">{{
                    __("forms")["duration"]
                }}</label>
                <select
                    v-model="duration"
                    class="form-select mt-1 w-full dark:bg-gray-400"
                >
                    <option :value="null" />
                    <option value="prevweek" class="dark:text-white">
                        {{ __("forms")["prevweek"] }}
                    </option>
                    <option value="prevmonth" class="dark:text-white">
                        {{ __("forms")["prevmonth"] }}
                    </option>
                    <option value="prevyear" class="dark:text-white">
                        {{ __("forms")["prevyear"] }}
                    </option>
                    <option value="thisweek" class="dark:text-white">
                        {{ __("forms")["thisweek"] }}
                    </option>

                    <option value="thismonth" class="dark:text-white">
                        {{ __("forms")["thismonth"] }}
                    </option>

                    <option value="thisyear" class="dark:text-white">
                        {{ __("forms")["thisyear"] }}
                    </option>
                </select>
            </date-filter>
        </div>
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="grid grid-cols-2 gap-4">
                    <div v-if="user.show_users_stats">
                        <Chart
                            v-if="rendered"
                            type="bar"
                            :chart-data="UserData"
                            :chart-options="UsersChartOptions"
                        />
                    </div>
                    <div v-if="user.show_customers_stats">
                        <Chart
                            v-if="rendered"
                            type="bar"
                            :chart-data="CustomerData"
                            :chart-options="CustomersChartOptions"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script>
export default {
    data() {
        return {};
    },
};
</script>
