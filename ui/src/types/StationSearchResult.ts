import StationSearchResultItem from './StationSearchResultItem.ts';

type StationSearchResult = {
    success: boolean;
    data: StationSearchResultItem[];
};

export default StationSearchResult;
