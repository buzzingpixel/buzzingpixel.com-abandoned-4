const urls = [
    {
        url: '/software/ansel-craft',
        titleContent: 'Ansel',
        subTitleContent: 'for ExpressionEngine',
        actionButtons: [
            {
                href: 'https://github.com/buzzingpixel/ansel-craft',
                content: 'GitHub',
            },
            {
                href: 'https://packagist.org/packages/buzzingpixel/ansel-craft',
                content: 'Packagist',
            },
            {
                href: 'https://plugins.craftcms.com/ansel',
                content: 'Plugin Store ($79)',
            },
        ],
    },
];

urls.forEach((item) => {
    describe(`Check the title area at URL: ${item.url}`, () => {
        let hasGoneToUrl = false;

        beforeEach(() => {
            if (!hasGoneToUrl) {
                cy.visit(item.url);

                cy.viewport('macbook-13');

                hasGoneToUrl = true;
            }
        });

        it('Is the first item under the menu', () => {
            cy.get('#SiteContent')
                .find('.SiteContent__Inner')
                .children()
                .eq(0)
                .should('have.class', 'TitleArea');
        });

        it('Title area inner has correct classes', () => {
            cy.get('#SiteContent')
                .find('.SiteContent__Inner')
                .children()
                .eq(0)
                .find('.TitleArea__Inner')
                .should(
                    item.actionButtons.length > 0 ? 'have.class' : 'not.have.class',
                    'TitleArea__Inner--HasActionButtons',
                );
        });

        it('Has title', () => {
            cy.get('#SiteContent')
                .find('.SiteContent__Inner')
                .children()
                .eq(0)
                .find('.TitleArea__Inner')
                .find('.TitleArea__Title')
                .should('have.class', 'heading')
                .should('have.class', 'heading--level-1')
                .should(
                    item.actionButtons.length > 0 ? 'have.class' : 'not.have.class',
                    'TitleArea__Title--HasActionButtons',
                )
                .contains(item.titleContent);
        });

        if (item.subTitleContent) {
            it('Has sub title', () => {
                cy.get('#SiteContent')
                    .find('.SiteContent__Inner')
                    .children()
                    .eq(0)
                    .find('.TitleArea__Inner')
                    .find('.TitleArea__Title')
                    .find('.TitleArea__Small')
                    .contains(item.subTitleContent);
            });
        }

        item.actionButtons.forEach((actionButton, i) => {
            const isLast = i === (item.actionButtons.length - 1);
            const finalButtonClass = isLast ? 'button--colored' : 'button--light';

            it('Has action button', () => {
                cy.get('#SiteContent')
                    .find('.SiteContent__Inner')
                    .children()
                    .eq(0)
                    .find('.TitleArea__Inner')
                    .find('.TitleArea__ActionButtons')
                    .children()
                    .eq(i)
                    .should('have.class', 'button')
                    .should('have.class', 'TitleArea__ActionButton')
                    .should('have.class', finalButtonClass)
                    .contains(actionButton.content)
                    .should('have.attr', 'href')
                    .should('include', actionButton.href);
            });
        });
    });
});
