import React from "react";
import usePageTitle from '../hooks/usePageTitle';
import HelpGuide from "../content/HelpGuide";

export default function PageGuide() {
    usePageTitle('Guide');

    return (
        <div className="page">
            <div className="container">
                <div className="row">
                    <div className="col-12">
                        <HelpGuide />
                    </div>
                </div>
            </div>
        </div>
    );
}
