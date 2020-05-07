const urls = [
    {
        url: '/software/ansel-craft',
        position: 0,
        hasHeadline: true,
        hasContent: true,
        ctaCount: 1,
    },
];

urls.forEach((item) => {
    describe(`Check the ImageCallOut module at URL: ${item.url}`, () => {
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
                .should('have.class', 'ImageCallOut');
        });

        if (item.hasHeadline) {
            it('Has headline', () => {
                cy.get('.Modules')
                    .children()
                    .eq(item.position)
                    .children()
                    .eq(0)
                    .should('have.class', 'ImageCallOut__Inner')
                    .children()
                    .eq(0)
                    .should('have.class', 'ImageCallOut__ContentArea')
                    .children()
                    .eq(contentEq)
                    .should('have.class', 'ImageCallOut__Heading');

                contentEq += 1;
            });
        }

        if (item.hasContent) {
            it('Has content', () => {
                cy.get('.Modules')
                    .children()
                    .eq(item.position)
                    .children()
                    .eq(0)
                    .should('have.class', 'ImageCallOut__Inner')
                    .children()
                    .eq(0)
                    .should('have.class', 'ImageCallOut__ContentArea')
                    .children()
                    .eq(contentEq)
                    .should('have.class', 'ImageCallOut__Content');

                contentEq += 1;
            });
        }

        if (item.ctaCount) {
            it('Has ctas', () => {
                cy.get('.Modules')
                    .children()
                    .eq(item.position)
                    .children()
                    .eq(0)
                    .should('have.class', 'ImageCallOut__Inner')
                    .children()
                    .eq(0)
                    .should('have.class', 'ImageCallOut__ContentArea')
                    .children()
                    .eq(contentEq)
                    .should('have.class', 'ImageCallOut__CTAs')
                    .children()
                    .should('have.length', item.ctaCount)
                    .each(($el) => {
                        expect($el).to.have.class('ImageCallOut__CTAButton');
                        expect($el).to.have.attr('href');
                    });

                contentEq += 1;
            });
        }

        it('Has image', () => {
            cy.get('.Modules')
                .children()
                .eq(item.position)
                .children()
                .eq(0)
                .should('have.class', 'ImageCallOut__Inner')
                .children()
                .eq(1)
                .should('have.class', 'ImageCallOut__ImageWrapper')
                .children()
                .eq(0)
                .find('img')
                .should('have.class', 'ImageCallOut__ImageTag');

            cy.get('.Modules')
                .children()
                .eq(item.position)
                .children()
                .eq(0)
                .should('have.class', 'ImageCallOut__Inner')
                .children()
                .eq(1)
                .should('have.class', 'ImageCallOut__ImageWrapper')
                .children()
                .eq(0)
                .find('img')
                .should('have.attr', 'src');

            cy.get('.Modules')
                .children()
                .eq(item.position)
                .children()
                .eq(0)
                .should('have.class', 'ImageCallOut__Inner')
                .children()
                .eq(1)
                .should('have.class', 'ImageCallOut__ImageWrapper')
                .children()
                .eq(0)
                .find('img')
                .should('have.attr', 'alt');
        });
    });
});
