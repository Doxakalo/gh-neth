import React from "react";
import usePageTitle from '../hooks/usePageTitle';
import GameTipsContent from "../content/GamePlanContent";


export default function PageGameTips() {
    usePageTitle('Game Tips');

    return (
        <div className="page">
            <div className="container">
                <div className="row">
                    <div className="col-12">
                        <GameTipsContent />
                    </div>
                </div>
            </div>
        </div>
    );
}
