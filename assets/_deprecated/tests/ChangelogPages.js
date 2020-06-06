const urls = [
    {
        url: '/software/ansel-craft/changelog',
        hasPagination: true,
    },
    {
        url: '/software/ansel-craft/changelog/page/2',
        hasPagination: true,
    },
];

urls.forEach((item) => {
    describe(`Check the changelog page at URL: ${item.url}`, () => {
        let hasGoneToUrl = false;

        beforeEach(() => {
            if (!hasGoneToUrl) {
                cy.visit(item.url);

                cy.viewport('macbook-13');

                hasGoneToUrl = true;
            }
        });

        if (item.hasPagination) {
            it('Has pagination at top', () => {
                cy.get('.SiteContent')
                    .children()
                    .eq(0)
                    .should('have.class', 'SiteContent__Inner')
                    .children()
                    .eq(2)
                    .should('have.class', 'StandardPageContent')
                    .should('have.class', 'StandardPageContent--Max1600')
                    .children()
                    .eq(0)
                    .should('have.class', 'StandardPageContent__Inner')
                    .children()
                    .eq(0)
                    .should('have.class', 'Pagination');
            });

            it('Has pagination at bottom', () => {
                cy.get('.SiteContent')
                    .children()
                    .eq(0)
                    .should('have.class', 'SiteContent__Inner')
                    .children()
                    .eq(2)
                    .should('have.class', 'StandardPageContent')
                    .should('have.class', 'StandardPageContent--Max1600')
                    .children()
                    .eq(0)
                    .should('have.class', 'StandardPageContent__Inner')
                    .children()
                    .eq(2)
                    .should('have.class', 'Pagination');
            });
        }

        it('Has content', () => {
            cy.get('.SiteContent')
                .children()
                .eq(0)
                .should('have.class', 'SiteContent__Inner')
                .children()
                .eq(2)
                .should('have.class', 'StandardPageContent')
                .should('have.class', 'StandardPageContent--Max1600')
                .children()
                .eq(0)
                .should('have.class', 'StandardPageContent__Inner')
                .children()
                .eq(item.hasPagination ? 1 : 0)
                .should('have.class', 'SidebarContent')
                .children()
                .eq(0)
                .should('have.class', 'SidebarContent__Sidebar')
                .children()
                .eq(0)
                .should('have.class', 'SidebarContent__Links')
                .children()
                .should(($els) => {
                    expect($els).to.have.length.greaterThan(0);
                })
                .each(($el) => {
                    cy.get($el)
                        .should('have.class', 'SidebarContent__Link')
                        .children()
                        .eq(0)
                        .should('have.class', 'SidebarContent__LinkTag')
                        .should('have.attr', 'href');
                });

            cy.get('.SiteContent')
                .children()
                .eq(0)
                .should('have.class', 'SiteContent__Inner')
                .children()
                .eq(2)
                .should('have.class', 'StandardPageContent')
                .should('have.class', 'StandardPageContent--Max1600')
                .children()
                .eq(0)
                .should('have.class', 'StandardPageContent__Inner')
                .children()
                .eq(1)
                .should('have.class', 'SidebarContent')
                .children()
                .eq(1)
                .should('have.class', 'SidebarContent__Body')
                .children()
                .should(($els) => {
                    expect($els).to.have.length.greaterThan(0);
                })
                .each(($el) => {
                    cy.get($el).should('have.class', 'BoxContainer')
                        .children()
                        .eq(0)
                        .should('have.class', 'Box')
                        .should('have.class', 'Box--HasSidebar')
                        .children()
                        .eq(0)
                        .should('have.class', 'Box__Inner')
                        .should('have.class', 'Box__Inner--HasSidebar')
                        .children()
                        .eq(0)
                        .should('have.class', 'Box__Sidebar')
                        .children()
                        .eq(0)
                        .should('have.class', 'Box__SidebarInner')
                        .children()
                        .eq(0)
                        .should('have.class', 'Box__SidebarList')
                        .children()
                        .should(($els) => {
                            expect($els).to.have.length(3);
                        })
                        .each(($listEl) => {
                            cy.get($listEl).should('have.class', 'Box__SidebarListItem');
                        });

                    cy.get($el).should('have.class', 'BoxContainer')
                        .children()
                        .eq(0)
                        .should('have.class', 'Box')
                        .should('have.class', 'Box--HasSidebar')
                        .children()
                        .eq(0)
                        .should('have.class', 'Box__Inner')
                        .should('have.class', 'Box__Inner--HasSidebar')
                        .children()
                        .eq(1)
                        .should('have.class', 'Box__Content')
                        .should('have.class', 'Box__Content--HasSidebar')
                        .children()
                        .eq(0)
                        .should('have.class', 'Box__ContentInner');
                });
        });
    });
});
