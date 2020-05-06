const urls = [
    {
        url: '/software/ansel-craft',
        position: 1,
        blocks: [
            {
                nameHref: false,
                positionHref: true,
            },
            {
                nameHref: false,
                positionHref: true,
            },
            {
                nameHref: true,
                positionHref: false,
            },
        ],
    },
];

urls.forEach((item) => {
    describe(`Check the QuoteBlocks module at URL: ${item.url}`, () => {
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
                .should('have.class', 'QuoteBlocks');
        });

        item.blocks.forEach((block, i) => {
            it(`Check block at position ${i}`, () => {
                cy.get('.Modules')
                    .children()
                    .eq(item.position)
                    .should('have.class', 'QuoteBlocks')
                    .children()
                    .eq(0)
                    .should('have.class', 'QuoteBlocks__Inner')
                    .children()
                    .eq(i)
                    .should('have.class', 'QuoteBlocks__Block')
                    .children()
                    .eq(0)
                    .should('have.class', 'QuoteBlocks__BlockInner')
                    .children()
                    .should('have.length', 2)
                    .each(($el, index) => {
                        if (index === 0) {
                            cy.get($el)
                                .should('have.class', 'QuoteBlocks__BlockHeader')
                                .children()
                                .eq(0)
                                .should('have.class', 'QuoteBlocks__BlockImage')
                                .children()
                                .eq(0)
                                .children()
                                .eq(0)
                                .should(($innerEl) => {
                                    expect($innerEl).to.have.class('QuoteBlocks__BlockImageTag');
                                    expect($innerEl).to.have.attr('src');
                                    expect($innerEl).to.have.attr('srcset');
                                });

                            return;
                        }

                        cy.get($el).should('have.class', 'QuoteBlocks__BlockQuote');
                    });
            });
        });
    });
});
