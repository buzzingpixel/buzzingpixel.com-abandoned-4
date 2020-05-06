const urls = [
    {
        url: '/software/ansel-craft',
        navItemsLeft: [
            {
                href: '/software/ansel-craft',
                content: 'Features',
                isActive: true,
            },
            {
                href: '/software/ansel-craft/changelog',
                content: 'Changelog',
                isActive: false,
            },
        ],
        navItemsRight: [
            {
                href: 'software/ansel-craft/documentation',
                content: 'Documentation',
                isActive: false,
            },
        ],
    },
    {
        url: '/software/ansel-craft/changelog',
        navItemsLeft: [
            {
                href: '/software/ansel-craft',
                content: 'Features',
                isActive: false,
            },
            {
                href: '/software/ansel-craft/changelog',
                content: 'Changelog',
                isActive: true,
            },
        ],
        navItemsRight: [
            {
                href: '/software/ansel-craft/documentation',
                content: 'Documentation',
                isActive: false,
            },
        ],
    },
    {
        url: '/software/ansel-craft/changelog/page/2',
        navItemsLeft: [
            {
                href: '/software/ansel-craft',
                content: 'Features',
                isActive: false,
            },
            {
                href: '/software/ansel-craft/changelog',
                content: 'Changelog',
                isActive: true,
            },
        ],
        navItemsRight: [
            {
                href: '/software/ansel-craft/documentation',
                content: 'Documentation',
                isActive: false,
            },
        ],
    },
    {
        url: '/software/ansel-craft/changelog/2.1.4',
        navItemsLeft: [
            {
                href: '/software/ansel-craft',
                content: 'Features',
                isActive: false,
            },
            {
                href: '/software/ansel-craft/changelog',
                content: 'Changelog',
                isActive: true,
            },
        ],
        navItemsRight: [
            {
                href: '/software/ansel-craft/documentation',
                content: 'Documentation',
                isActive: false,
            },
        ],
    },
];

urls.forEach((item) => {
    describe(`Check the sub title page nav area at URL: ${item.url}`, () => {
        let hasGoneToUrl = false;

        beforeEach(() => {
            if (!hasGoneToUrl) {
                cy.visit(item.url);

                cy.viewport('macbook-13');

                hasGoneToUrl = true;
            }
        });

        it('Is the second item under the menu', () => {
            cy.get('#SiteContent')
                .find('.SiteContent__Inner')
                .children()
                .eq(1)
                .should('have.class', 'SubTitlePageNav');
        });

        if (item.navItemsLeft.length > 0) {
            item.navItemsLeft.forEach((navItem, i) => {
                it('Has nav item', () => {
                    cy.get('#SiteContent')
                        .find('.SiteContent__Inner')
                        .children()
                        .eq(1)
                        .find('.SubTitlePageNav__Inner')
                        .find('.SubTitlePageNav__NavItems')
                        .eq(0)
                        .should('have.class', 'SubTitlePageNav__NavItems--IsOnLeftSide')
                        .find('.SubTitlePageNav__NavItem')
                        .eq(i)
                        .find('.SubTitlePageNav__NavItemLink')
                        .should(
                            navItem.isActive ? 'have.class' : 'not.have.class',
                            'SubTitlePageNav__NavItemLink--IsActive',
                        )
                        .should('have.attr', 'href')
                        .should('include', navItem.href);
                });
            });
        }

        if (item.navItemsRight.length > 0) {
            item.navItemsRight.forEach((navItem, i) => {
                it('Has nav item', () => {
                    cy.get('#SiteContent')
                        .find('.SiteContent__Inner')
                        .children()
                        .eq(1)
                        .find('.SubTitlePageNav__Inner')
                        .find('.SubTitlePageNav__NavItems')
                        .eq(1)
                        .should('have.class', 'SubTitlePageNav__NavItems--IsOnRightSide')
                        .find('.SubTitlePageNav__NavItem')
                        .eq(i)
                        .find('.SubTitlePageNav__NavItemLink')
                        .should(
                            navItem.isActive ? 'have.class' : 'not.have.class',
                            'SubTitlePageNav__NavItemLink--IsActive',
                        )
                        .should('have.attr', 'href')
                        .should('include', navItem.href);
                });
            });
        }
    });
});
