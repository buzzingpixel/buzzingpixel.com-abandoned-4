const urls = {
    '/': 2,
};

Object.keys(urls).forEach((url) => {
    const pos = urls[url];

    describe(`Check that the InformationalImage module exists and is at position ${pos} at URL: ${url}`, () => {
        let hasgoneToUrl = false;

        beforeEach(() => {
            if (!hasgoneToUrl) {
                cy.visit(url);

                cy.viewport('macbook-13');

                hasgoneToUrl = true;
            }
        });

        it(`Is in position ${pos}`, () => {
            cy.get('.Modules').children().eq(pos)
                .should('have.class', 'InformationalImage');
        });

        it('Contains headline', () => {
            cy.get('.Modules').children().eq(pos)
                .find('.InformationalImage__Headline')
                .should('have.length', 1);
        });

        it('Contains headline', () => {
            cy.get('.Modules').children().eq(pos)
                .find('.InformationalImage__SubHeading')
                .should('have.length', 1);
        });

        it('Contains content', () => {
            cy.get('.Modules').children().eq(pos)
                .find('.InformationalImage__Content')
                .should('have.length', 1);
        });

        it('Contains image', () => {
            cy.get('.Modules').children().eq(pos)
                .find('.InformationalImage__Image')
                .should('have.length', 1);
        });
    });
});
