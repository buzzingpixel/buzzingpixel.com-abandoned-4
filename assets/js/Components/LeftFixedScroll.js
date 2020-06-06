function positionCheck (data) {
    const scrollTop = window.scrollY || document.documentElement.scrollTop;
    data.isSticky = scrollTop > parseInt(data.stickyAt, 10);
}

export default (data) => {
    positionCheck(data);

    setTimeout(() => {
        positionCheck(data);
    }, 1000);

    window.addEventListener('scroll', () => {
        positionCheck(data);
    });
};
