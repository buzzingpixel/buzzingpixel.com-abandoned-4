const urls = {
    '/': 0,
};

Object.keys(urls).forEach((url) => {
    const pos = urls[url];

    describe(`Check that the showcase exists and is at position ${pos} at URL: ${url}`, () => {
        let hasGoneToUrl = false;

        beforeEach(() => {
            if (!hasGoneToUrl) {
                cy.visit('/');

                hasGoneToUrl = true;
            }

            cy.viewport('macbook-13');
        });

        it(`Is visible in position ${pos}`, () => {
            const $module = cy.get('.Modules').eq(0);
            $module.should('be.visible');
        });

        it('Contains pre headline', () => {
            const $module = cy.get('.Modules').eq(0);
            $module.find('.ShowCase__PreHeadline').should('be.visible');
        });

        it('Contains headline', () => {
            const $module = cy.get('.Modules').eq(0);
            $module.find('.ShowCase__Headline').should('be.visible');
        });

        it('Contains sub headline', () => {
            const $module = cy.get('.Modules').eq(0);
            $module.find('.ShowCase__SubHeadline').should('be.visible');
        });

        it('Contains ctas', () => {
            const $module = cy.get('.Modules').eq(0);
            $module.find('.ShowCase__Ctas').should('be.visible');
        });

        it('Contains the show case image', () => {
            const $module = cy.get('.Modules').eq(0);
            $module.find('.ShowCase__ImageWrapper').should('be.visible');
        });
    });
});
