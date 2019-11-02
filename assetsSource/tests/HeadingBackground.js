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
        const img = imgForUrlMap[url];

        beforeEach(() => {
            cy.viewport('iphone-x');
            cy.visit('/');
        });

        it('Is visible', () => {
            cy.get('.HeadingBackground').should('be.visible');
        });

        it('Has correct image', () => {
            const $headingBackground = cy.get('.HeadingBackground');
            const $img = $headingBackground.find('.HeadingBackground__Img');

            $img.should('have.attr', 'src')
                .should('include', img.src);

            if (img.alt) {
                $img.should('have.attr', 'alt')
                    .should('include', img.alt);
            }

            if (img.srcset) {
                $img.should('have.attr', 'srcset')
                    .should('include', img.srcset);
            }
        });
    });
});
