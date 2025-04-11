export function formatDate(d) {
    const date = new Date(d);
    const yyyy = date.getFullYear();
    let mm = date.getMonth() + 1;
    let dd = date.getDate();

    if (dd < 10) dd = "0" + dd;
    if (mm < 10) mm = "0" + mm;

    const formattedToday = yyyy + "-" + mm + "-" + dd;
    return formattedToday;
}

export function formatTime(d) {
    const date = new Date(d);
    let H = date.getHours();
    let i = date.getMinutes();

    if (H < 10) H = "0" + H;
    if (i < 10) i = "0" + i;

    const formattedToday = H + ":" + i;
    return formattedToday;
}

export function formatTimetoLacle(d) {
    return new Date(d).toLocaleTimeString("en-GB", {
        timeZone: "UTC",
    });
}
