const urls = {
    '/': 1,
};

Object.keys(urls).forEach((url) => {
    const pos = urls[url];

    describe(`Check that the CtaCards module exists and is at position ${pos} at URL: ${url}`, () => {
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
                .should('have.class', 'CtaCards');
        });

        it('Has 3 cards', () => {
            cy.get('.Modules').children().eq(pos)
                .find('.CtaCards__Inner')
                .find('.CtaCards__Item')
                .should('have.length', 3);
        });

        it('Items have headline', () => {
            let length = 0;

            cy.get('.Modules').children().eq(pos)
                .find('.CtaCards__Inner')
                .find('.CtaCards__Item')
                .each(($el) => {
                    cy.get($el).find('.CtaCards__ItemTitle')
                        .should('have.length', 1);

                    length += 1;
                })
                .then(() => {
                    assert.equal(3, length);
                });
        });

        it('Items have body', () => {
            let length = 0;

            cy.get('.Modules').children().eq(pos)
                .find('.CtaCards__Inner')
                .find('.CtaCards__Item')
                .each(($el) => {
                    cy.get($el).find('.CtaCards__ItemBody')
                        .should('have.length', 1);

                    length += 1;
                })
                .then(() => {
                    assert.equal(3, length);
                });
        });
    });
});
