const urls = {
    '/': 0,
};

Object.keys(urls).forEach((url) => {
    const pos = urls[url];

    describe(`Check that the showcase exists and is at position ${pos} at URL: ${url}`, () => {
        let hasGoneToUrl = false;

        beforeEach(() => {
            if (!hasGoneToUrl) {
                cy.visit(url);

                cy.viewport('macbook-13');

                hasGoneToUrl = true;
            }
        });

        it(`Is in position ${pos}`, () => {
            cy.get('.Modules').children().eq(pos)
                .should('have.class', 'ShowCase');

            if (pos === 0) {
                cy.get('.Modules').children().eq(pos)
                    .should('have.class', 'ShowCase--IsFirst');
            }
        });

        it('Contains pre headline', () => {
            cy.get('.Modules').children().eq(pos)
                .find('.ShowCase__PreHeadline')
                .should('have.length', 1);
        });

        it('Contains headline', () => {
            cy.get('.Modules').children().eq(pos)
                .find('.ShowCase__Headline')
                .should('have.length', 1);
        });

        it('Contains sub headline', () => {
            cy.get('.Modules').children().eq(pos)
                .find('.ShowCase__SubHeadline')
                .should('have.length', 1);
        });

        it('Contains ctas', () => {
            cy.get('.Modules').children().eq(pos)
                .find('.ShowCase__Ctas')
                .should('have.length', 1);
        });

        it('Contains the show case image', () => {
            cy.get('.Modules').children().eq(pos)
                .find('.ShowCase__ImageWrapper')
                .should('have.length', 1);
        });
    });
});
