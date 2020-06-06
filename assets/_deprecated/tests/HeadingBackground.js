const urls = [
    '/',
];

const imgForUrlMap = {
    '/': {
        src: '/content-images/eyes-background-image/eyes.jpg',
        srcset: '',
        alt: '',
    },
};

urls.forEach((url) => {
    describe(`Check that heading background exists at ${url}`, () => {
        const hasGoneToUrl = false;
        const img = imgForUrlMap[url];

        beforeEach(() => {
            if (!hasGoneToUrl) {
                cy.visit(url);

                cy.viewport('iphone-x');
            }
        });

        it('Is visible', () => {
            cy.get('.HeadingBackground').should('have.length', 1);
        });

        it('Has correct image', () => {
            cy.get('.HeadingBackground')
                .find('.HeadingBackground__Img')
                .should('have.attr', 'src')
                .should('include', img.src);

            if (img.alt) {
                cy.get('.HeadingBackground')
                    .find('.HeadingBackground__Img')
                    .should('have.attr', 'alt')
                    .should('include', img.alt);
            }

            if (img.srcset) {
                cy.get('.HeadingBackground')
                    .find('.HeadingBackground__Img')
                    .should('have.attr', 'srcset')
                    .should('include', img.srcset);
            }
        });
    });
});
