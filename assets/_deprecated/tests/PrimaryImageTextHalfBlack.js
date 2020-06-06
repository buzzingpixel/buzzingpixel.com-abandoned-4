const urls = [
    {
        url: '/software/ansel-craft',
        position: 2,
        hasHeadline: true,
        hasContent: true,
    },
];

urls.forEach((item) => {
    describe(`Check the Primary Image Text Half Black module at URL: ${item.url}`, () => {
        let hasGoneToUrl = false;
        let contentEq = 0;

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
                .should('have.class', 'PrimaryImageTextHalfBlack');
        });

        if (item.hasHeadline) {
            it('Has headline', () => {
                cy.get('.Modules')
                    .children()
                    .eq(item.position)
                    .should('have.class', 'PrimaryImageTextHalfBlack')
                    .children()
                    .eq(0)
                    .should('have.class', 'PrimaryImageTextHalfBlack__Inner')
                    .children()
                    .eq(contentEq)
                    .should('have.class', 'PrimaryImageTextHalfBlack__Heading');

                contentEq += 1;
            });
        }

        it('Has image', () => {
            cy.get('.Modules')
                .children()
                .eq(item.position)
                .should('have.class', 'PrimaryImageTextHalfBlack')
                .children()
                .eq(0)
                .should('have.class', 'PrimaryImageTextHalfBlack__Inner')
                .children()
                .eq(contentEq)
                .should('have.class', 'PrimaryImageTextHalfBlack__ImageContainer')
                .children()
                .eq(0)
                .should('have.class', 'PrimaryImageTextHalfBlack__Image')
                .children()
                .eq(0)
                .find('img')
                .should('have.length', 1)
                .each(($el) => {
                    cy.get($el).should('have.class', 'PrimaryImageTextHalfBlack__ImageTag');
                    cy.get($el).should('have.attr', 'src');
                    cy.get($el).should('have.attr', 'srcset');
                    cy.get($el).should('have.attr', 'alt');
                });

            contentEq += 1;
        });

        if (item.hasContent) {
            it('Has content', () => {
                cy.get('.Modules')
                    .children()
                    .eq(item.position)
                    .should('have.class', 'PrimaryImageTextHalfBlack')
                    .children()
                    .eq(0)
                    .should('have.class', 'PrimaryImageTextHalfBlack__Inner')
                    .children()
                    .eq(contentEq)
                    .should('have.class', 'PrimaryImageTextHalfBlack__Content')
                    .children()
                    .eq(0)
                    .should('have.class', 'PrimaryImageTextHalfBlack__ContentInner');

                contentEq += 1;
            });
        }
    });
});
