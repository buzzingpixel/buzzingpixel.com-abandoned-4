const urls = [
    '/',
];

urls.forEach((url) => {
    describe(`Check main menu functionality at ${url}`, () => {
        beforeEach(() => {
            cy.viewport('iphone-x');
            cy.visit('/');
        });

        it('Loads with mobile menu hidden', () => {
            cy.viewport('iphone-x');
            cy.get('.SiteNav__Logo').should('be.visible');
            cy.get('.SiteNav__List').should('be.visible').should('not.have.class', 'SiteNav__List--MobileMenuIsActive');
            cy.get('.SiteNav__Icons').should('be.visible');
            cy.get('.SiteNav__MobileOpener').should('be.visible');
        });

        it('Opens and closes mobile menu', () => {
            cy.viewport('iphone-x');
            cy.get('.SiteNav__List').should('not.have.class', 'SiteNav__List--MobileMenuIsActive');
            cy.get('.SiteNav__MobileOpener').click();
            cy.get('.SiteNav__List').should('have.class', 'SiteNav__List--MobileMenuIsActive');
            cy.get('.SiteNav__MobileOpener').click();
            cy.get('.SiteNav__List').should('not.have.class', 'SiteNav__List--MobileMenuIsActive');
        });

        it('Opens and closes mobile menu just before break point', () => {
            cy.viewport(559, 800);
            cy.get('.SiteNav__List').should('not.have.class', 'SiteNav__List--MobileMenuIsActive');
            cy.get('.SiteNav__MobileOpener').click();
            cy.get('.SiteNav__List').should('have.class', 'SiteNav__List--MobileMenuIsActive');
            cy.get('.SiteNav__MobileOpener').click();
            cy.get('.SiteNav__List').should('not.have.class', 'SiteNav__List--MobileMenuIsActive');
        });

        it('Closes open mobile menu at break point', () => {
            cy.viewport(559, 800);
            cy.get('.SiteNav__MobileOpener').click();
            cy.get('.SiteNav__List').should('have.class', 'SiteNav__List--MobileMenuIsActive');
            cy.viewport(600, 800);
            cy.get('.SiteNav__List').should('not.have.class', 'SiteNav__List--MobileMenuIsActive');
        });

        it('Opens subnav menus at desktop widths', () => {
            cy.viewport(600, 800);
            cy.get('.SiteNav__MobileOpener').should('not.be.visible');
            cy.get('.SiteNav__List').should('not.have.class', 'SiteNav__List--MobileMenuIsActive');
            cy.get('.SiteNav__ListItem')
                .each(($el) => {
                    const $listItem = $el.closest('.SiteNav__ListItem');
                    const $menu = $listItem.find('.SiteNav__ListLevel2');
                    const $siteNav = $el.closest('.SiteNav');
                    expect($menu).not.to.have.class('SiteNav__ListLevel2--IsActive');
                    $listItem.trigger('mouseenter');
                    // Unfortunately this doesn't really work with cypress right now
                    // expect($listItem.trigger('mouseenter'))
                    // .to.have.class('SiteNav__ListLevel2--IsActive');
                    $siteNav.trigger('mouseleave');
                    cy.wait(500);
                    expect($menu).not.to.have.class('SiteNav__ListLevel2--IsActive');
                })
                .then(($els) => {
                    expect($els).to.have.length(2);
                });
        });
    });
});
