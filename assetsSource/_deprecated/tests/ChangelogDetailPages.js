const urls = [
    {
        url: '/software/ansel-craft/changelog/2.1.4',
        breadcrumbs: [
            {
                href: '/',
                content: 'Home',
            },
            {
                href: '/software/ansel-craft',
                content: 'Ansel for Craft CMS',
            },
            {
                href: '/software/ansel-craft/changelog',
                content: 'Changelog',
            },
            {
                content: 'Version 2.1.4',
            },
        ],
    },
    {
        url: '/software/ansel-craft/changelog/1.0.2',
        breadcrumbs: [
            {
                href: '/',
                content: 'Home',
            },
            {
                href: '/software/ansel-craft',
                content: 'Ansel for Craft CMS',
            },
            {
                href: '/software/ansel-craft/changelog',
                content: 'Changelog',
            },
            {
                content: 'Version 1.0.2',
            },
        ],
    },
];

urls.forEach((item) => {
    describe(`Check the changelog detail page at URL: ${item.url}`, () => {
        let hasGoneToUrl = false;

        beforeEach(() => {
            if (!hasGoneToUrl) {
                cy.visit(item.url);

                cy.viewport('macbook-13');

                hasGoneToUrl = true;
            }
        });

        it('Has breadcrumbs', () => {
            cy.get('.SiteContent')
                .children()
                .eq(0)
                .should('have.class', 'SiteContent__Inner')
                .children()
                .eq(2)
                .should('have.class', 'BreadCrumbs')
                .children()
                .should('have.length', item.breadcrumbs.length)
                .each(($el, i) => {
                    const $anchorTag = $el.children();
                    const breadcrumbDef = item.breadcrumbs[i];
                    const { href, content } = breadcrumbDef;

                    expect($el).to.have.class('BreadCrumbs__Item');

                    expect($anchorTag).to.have.length(1);

                    expect($anchorTag).to.have.class('BreadCrumbs__ItemLink');

                    expect($anchorTag).to.contain(content);

                    if (!href) {
                        expect($anchorTag).to.have.class('BreadCrumbs__ItemLink--IsInactive');

                        return;
                    }

                    expect($anchorTag).not.to.have.class('BreadCrumbs__ItemLink--IsInactive');

                    expect($anchorTag).to.have.attr('href', item.breadcrumbs[i].href);
                });
        });

        it('Has content', () => {
            cy.get('.SiteContent')
                .children()
                .eq(0)
                .should('have.class', 'SiteContent__Inner')
                .children()
                .eq(3)
                .should('have.class', 'StandardPageContent')
                .should('have.class', 'StandardPageContent--Max1600')
                .children()
                .should('have.length', 1)
                .eq(0)
                .should('have.class', 'BoxContainer')
                .children()
                .should('have.length', 1)
                .eq(0)
                .should('have.class', 'Box')
                .should('have.class', 'Box--HasSidebar')
                .children()
                .should('have.length', 1)
                .eq(0)
                .should('have.class', 'Box__Inner')
                .should('have.class', 'Box__Inner--HasSidebar')
                .children()
                .should('have.length', 2)
                .each(($el, i) => {
                    if (i === 0) {
                        const $sidebarInnerQuery = $el.children();
                        const $sidebarInner = $sidebarInnerQuery.eq(0);
                        const $sidebarListQuery = $sidebarInner.children();
                        const $sidebarList = $sidebarListQuery.eq(0);
                        const $sidebarListItemsQuery = $sidebarList.children();
                        const $listItem1 = $sidebarListItemsQuery.eq(0);
                        const $listItem2 = $sidebarListItemsQuery.eq(1);
                        const $listItem3 = $sidebarListItemsQuery.eq(2);

                        expect($el).to.have.class('Box__Sidebar');

                        expect($sidebarInnerQuery).to.have.length(1);

                        expect($sidebarInner).to.have.class('Box__SidebarInner');

                        expect($sidebarListQuery).to.have.length(1);

                        expect($sidebarList).to.have.class('Box__SidebarList');

                        expect($sidebarListItemsQuery).to.have.length(3);

                        expect($listItem1).to.have.class('Box__SidebarListItem');

                        expect($listItem2).to.have.class('Box__SidebarListItem');

                        expect($listItem3).to.have.class('Box__SidebarListItem');

                        return;
                    }

                    const $contentInnerQuery = $el.children();
                    const $contentInner = $contentInnerQuery.eq(0);

                    expect($el).to.have.class('Box__Content');

                    expect($el).to.have.class('Box__Content--HasSidebar');

                    expect($contentInnerQuery).to.have.length(1);

                    expect($contentInner).to.have.class('Box__ContentInner');
                });
        });
    });
});
