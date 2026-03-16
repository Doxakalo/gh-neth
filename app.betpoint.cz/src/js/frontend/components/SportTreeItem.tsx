import React, { useState } from "react";
import { useDispatch, useSelector } from 'react-redux';
import { Sport, SportCategory } from '../types/SportTypes';
import { setSportAndCategory, SportTreeState } from '../redux/sportTreeSlice';

interface Props {
	sport: Sport;
};

export default function SportTreeItem({ sport }: Props) {
	const dispatch = useDispatch();
	const sportTreeState = useSelector((state: { sportTree: SportTreeState }) => state.sportTree);
	const hasCategories = sport.categories && sport.categories.length > 0;
	const [open, setOpen] = useState<boolean>(sportTreeState.sport?.id === sport.id);

	const handleClick = (sport: Sport, category: SportCategory) => {
		dispatch(setSportAndCategory({
			sport: sport,
			category: category
		}));
	};

	const isCategorySelected = (category: SportCategory) => {
		return sportTreeState.sportCategory && sportTreeState.sportCategory.id === category.id;
	}

	return (
		<div className={`sport-tree-item ${open ? 'open' : ''} ${!hasCategories ? 'disabled' : ''}`}>
			<div className="heading" onClick={() => hasCategories && setOpen(!open)}>
				<div className="title">
					<i className={`icon sport-icon sbc-icon-sport-${sport.alias}`}></i>
					<span>{sport.name}</span>
				</div>
				<i className="icon caret sbc-icon-caret-right"></i>
			</div>

			<ul className='categories'>
				{sport.categories.map((category: SportCategory) => {
					const nameCount = sport.categories.filter(c => c.name === category.name).length;
					const displayName = (nameCount > 1 || category.twice_enabled) && category.country_name
						? `${category.name} (${category.country_name})`
						: category.name;

					return (
						<li
							key={category.id}
							className={`sport-category-item ${isCategorySelected(category) ? 'selected' : ''}`}
							onClick={() => handleClick(sport, category)}
						>
							<span className="name">{displayName}</span>
							<span className="count" title='Available matches'>{category.match_count}</span>
						</li>
					);
				})}
			</ul>
		</div>
	);
}
