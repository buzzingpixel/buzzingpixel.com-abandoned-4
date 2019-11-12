const urls = [
    {
        url: '/software/ansel-craft',
        position: 4,
        backgroundColor: 'SpringWood',
        columns: [
            {
                hasHeadline: true,
                hasContent: true,
            },
            {
                hasHeadline: true,
                hasContent: true,
            },
            {
                hasHeadline: true,
                hasContent: true,
            },
            {
                hasHeadline: true,
                hasContent: true,
            },
        ],
    },
];

urls.forEach((item) => {
    describe(`Check the Image module at URL: ${item.url}`, () => {
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
                .should('have.class', 'TextColumns');
        });

        if (item.backgroundColor) {
            it('Has background color', () => {
                cy.get('.Modules')
                    .children()
                    .eq(item.position)
                    .should('have.class', `TextColumns--HasBackgroundColor${item.backgroundColor}`);
            });
        }

        item.columns.forEach((column, i) => {
            it(`Column is correct at position ${i}`, () => {
                cy.get('.Modules')
                    .children()
                    .eq(item.position)
                    .should('have.class', 'TextColumns')
                    .children()
                    .eq(0)
                    .should('have.class', 'TextColumns__Inner')
                    .children()
                    .eq(i)
                    .should('have.class', 'TextColumns__Column')
                    .children()
                    .should(
                        'have.length',
                        column.hasHeadline && column.hasContent ? 2 : 1,
                    )
                    .each(($el, index) => {
                        if (column.hasHeadline && column.hasContent) {
                            if (index === 0) {
                                cy.get($el).should('have.class', 'TextColumns__ColumnHeading');
                            }

                            return;
                        }

                        if (column.hasHeadline) {
                            cy.get($el).should('have.class', 'TextColumns__ColumnHeading');

                            return;
                        }

                        cy.get($el).should('have.class', 'TextColumns__Content');
                    });
            });
        });
    });
});
