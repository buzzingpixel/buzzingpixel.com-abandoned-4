const urls = [
    '/',
    '/software/ansel-craft',
];

urls.forEach((url) => {
    describe(`Check site content at ${url}`, () => {
        const hasGoneToUrl = false;

        beforeEach(() => {
            if (!hasGoneToUrl) {
                cy.visit(url);
            }
        });

        it('Has one site content', () => {
            cy.get('.SiteContent').should('have.length', 1);

            cy.get('.SiteMain').children().eq(1).should('have.class', 'SiteContent');

            cy.get('.SiteMain').children().eq(1).should('have.id', 'SiteContent');
        });
    });
});
