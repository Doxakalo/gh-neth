import React from "react";

export default function HelpGuide() {

    return (
        <>
            <h1>Betpoint Documentation</h1>
            <p>Index:</p>
            <ul>
                <li>Complaint about incorrectly evaluated match by data provider</li>
                <li>Adding or editing a user</li>
                <li>Adding a new category or disabling an existing one</li>
                <li>User navigation on app.betpoint.cz</li>
            </ul>
            <br />

            <h2>Complaint about incorrectly evaluated match</h2>
            <p>1. In case of an incorrectly evaluated match by the data provider, visit https://admin.app.betpoint.cz</p>
            <p>2. Go to the <strong>Matches</strong> section</p>
            <p>3. Find the match using the search options</p>
            <p>4. Click on the eye icon and enter values based on the real result of the match</p>
            <p style={{ paddingLeft: '2rem' }}>     - <strong>WARNING!</strong> The winnings and losses of betpoints will be recalculated</p>
            <br />

            <h2>Adding or editing a user</h2>
            <p>For both adding and editing a user:</p>
            <p>1. Go to https://admin.app.betpoint.cz</p>
            <p>2. Navigate to the <strong>Users</strong> section</p>

            <h3>Adding a user</h3>
            <p>1. Click <strong>Add User</strong></p>
            <p>2. Fill in the login credentials for the user</p>

            <h3>Editing a user</h3>
            <p>1. Find the user using the search options</p>
            <p>2. Click on the pencil icon</p>
            <p>3. Edit the user data to the desired values</p>

            <h2>Adding a new category</h2>
            <p>1. To add a new category to a sport, visit https://admin.betpoint.cz</p>
            <p>2. Go to the <strong>Categories</strong> section</p>
            <p>3. Find the category using the search options</p>
            <p>4. Click the checkbox to enable it</p>
            <p style={{ paddingLeft: '2rem' }}>     - <strong>WARNING!</strong> After enabling a category, you must synchronize categories and matches — this synchronization may take up to three hours</p>

            <h2>User navigation on app.betpoint.cz</h2>
            <h3>Sports section</h3>
            <p>1. Here, the user can find categories by expanding a sport</p>
            <p>2. After expanding a sport, the user sees a list of categories and the number of their matches</p>
            <p>3. After expanding a category, the user sees the matches available for betting</p>
            <p>4. After clicking on a match, the user sees the available betting options</p>

            <h3>My account section</h3>
            <p>Here, the user can see:</p>
            <p>1. Account details and the membership start date on the left and betcoin balance in the wallet on the right</p>
            <p>2. Total sum of pendings bets</p>
            <p>3. Bets and their evaluations</p>
            <p style={{ paddingLeft: '2rem' }}>     - By clicking on an evaluated bet, the user can fill out a form and submit a complaint regarding the evaluation</p>
            <p>4. Transaction history</p>

            <h3>Website footer</h3>
            <p>Here, the user can see:</p>
            <p><strong>1. Bets type and evaluation</strong></p>
            <p style={{ paddingLeft: '2rem' }}>     - Types of bets and explanations on how bet evaluations work for different sports</p>
            <p><strong>2. Privacy Policy</strong></p>
            <p style={{ paddingLeft: '2rem' }}>     - Personal data processing policy</p>
            <p><strong>3. Terms & Conditions</strong></p>
            <p style={{ paddingLeft: '2rem' }}>     - General terms of use of the www.app.betpoint.cz portal</p>
            <p><strong>4. Support / Contact us</strong></p>
            <p style={{ paddingLeft: '2rem' }}>     - Here, the user can fill out a form and contact the administrative staff of www.app.betpoint.cz</p>
        </>
    );
}
