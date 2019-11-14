const urls = [
    '/',
    '/software/ansel-craft',
    '/software/ansel-craft/changelog',
    '/software/ansel-craft/changelog/page/2',
];

urls.forEach((url) => {
    describe(`Check footer at ${url}`, () => {
        const hasGoneToUrl = false;

        beforeEach(() => {
            if (!hasGoneToUrl) {
                cy.visit(url);
            }
        });

        it('Has one footer', () => {
            cy.get('.SiteFooter').should('have.length', 1);

            cy.get('.SiteMain').children().eq(2).should('have.class', 'SiteFooter');
        });

        it('Footer has copyright', () => {
            const d = new Date();
            const year = d.getFullYear();

            cy.get('.SiteFooter').find('.SiteFooter__Inner')
                .find('.SiteFooter__Upper')
                .contains(`© ${year} BuzzingPixel, LLC`);
        });

        it('Has footer menu items', () => {
            cy.get('.SiteFooter').find('.SiteFooter__Inner')
                .find('.SiteFooter__Lower')
                .find('.SiteFooter__Link')
                .should('have.length', 3);

            cy.get('.SiteFooter').find('.SiteFooter__Inner')
                .find('.SiteFooter__Lower')
                .find('.SiteFooter__Link')
                .eq(0)
                .should('have.attr', 'href')
                .should('include', '/cookies');

            cy.get('.SiteFooter').find('.SiteFooter__Inner')
                .find('.SiteFooter__Lower')
                .find('.SiteFooter__Link')
                .eq(1)
                .should('have.attr', 'href')
                .should('include', '/privacy');

            cy.get('.SiteFooter').find('.SiteFooter__Inner')
                .find('.SiteFooter__Lower')
                .find('.SiteFooter__Link')
                .eq(2)
                .should('have.attr', 'href')
                .should('include', '/terms');
        });
    });
});
