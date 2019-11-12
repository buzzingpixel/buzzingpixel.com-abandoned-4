const urls = [
    {
        url: '/software/ansel-craft',
        position: 5,
        count: 2,
    },
];

urls.forEach((item) => {
    describe(`Check the Ctas module at URL: ${item.url}`, () => {
        let hasGoneToUrl = false;

        beforeEach(() => {
            if (!hasGoneToUrl) {
                cy.visit(item.url);

                cy.viewport('macbook-13');

                hasGoneToUrl = true;
            }
        });

        it(`Is in position ${item.position}`, () => {
            cy.get('.Modules')
                .children()
                .eq(item.position)
                .should('have.class', 'CTAs');
        });

        for (let i = 0; i < item.count; i += 1) {
            const last = i === (item.count - 1);

            it(`Check cta at position ${i}`, () => {
                cy.get('.Modules')
                    .children()
                    .eq(item.position)
                    .should('have.class', 'CTAs')
                    .children()
                    .eq(0)
                    .should('have.class', 'CTAs__Inner')
                    .children()
                    .eq(i)
                    .should('have.class', 'CTAs__Button')
                    .should(
                        'have.class',
                        last ? 'button--colored' : 'button--light',
                    )
                    .should(
                        'not.have.class',
                        last ? 'button--light' : 'button--colored',
                    )
                    .should('have.attr', 'href');
            });
        }
    });
});
