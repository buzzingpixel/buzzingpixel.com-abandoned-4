const urls = [
    '/',
];

urls.forEach((url) => {
    describe(`Check main menu functionality at ${url}`, () => {
        const hasGoneToUrl = false;

        beforeEach(() => {
            if (!hasGoneToUrl) {
                cy.visit(url);

                cy.viewport('iphone-x');
            }
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

            cy.wait(400);

            cy.get('.SiteNav__ListItem').should('have.length', 2);

            cy.get('.SiteNav__ListItem').eq(0).click();
            cy.wait(50);
            cy.get('.SiteNav__ListItem').eq(0).find('.SiteNav__ListLevel2').should('have.class', 'SiteNav__ListLevel2--IsActive');
            cy.wait(400);
            cy.get('#SiteContent').click({ force: true });
            cy.get('.SiteNav__ListItem').eq(0).find('.SiteNav__ListLevel2').should('not.have.class', 'SiteNav__ListLevel2--IsActive');

            cy.get('.SiteNav__ListItem').eq(1).click();
            cy.wait(50);
            cy.get('.SiteNav__ListItem').eq(1).find('.SiteNav__ListLevel2').should('have.class', 'SiteNav__ListLevel2--IsActive');
            cy.wait(50);
            cy.get('.SiteNav__ListItem').eq(0).click();
            cy.wait(50);
            cy.get('.SiteNav__ListItem').eq(1).find('.SiteNav__ListLevel2').should('not.have.class', 'SiteNav__ListLevel2--IsActive');
            cy.wait(50);
            cy.get('.SiteNav__ListItem').eq(0).click();
            cy.get('#SiteContent').click({ force: true });
            cy.get('.SiteNav__ListItem').eq(0).find('.SiteNav__ListLevel2').should('not.have.class', 'SiteNav__ListLevel2--IsActive');
            cy.get('.SiteNav__ListItem').eq(1).find('.SiteNav__ListLevel2').should('not.have.class', 'SiteNav__ListLevel2--IsActive');
        });
    });
});
