const urls = [
    {
        url: '/software/ansel-craft',
        position: 3,
        backgroundColor: 'SpringWood',
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
                .should('have.class', 'Image');
        });

        if (item.backgroundColor) {
            it('Has background color', () => {
                cy.get('.Modules')
                    .children()
                    .eq(item.position)
                    .should('have.class', `Image--HasBackgroundColor${item.backgroundColor}`);
            });
        }

        it('Has image tag', () => {
            cy.get('.Modules')
                .children()
                .eq(item.position)
                .should('have.class', 'Image')
                .children()
                .eq(0)
                .should('have.class', 'Image__Inner')
                .children()
                .eq(0)
                .find('img')
                .should('have.length', '1')
                .each(($el) => {
                    cy.get($el).should('have.class', 'Image__ImgTag');
                    cy.get($el).should('have.attr', 'alt');
                    cy.get($el).should('have.attr', 'src');
                    cy.get($el).should('have.attr', 'srcset');
                });
        });
    });
});
