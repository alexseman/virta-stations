import StationCoordinates from './StationCoordinates.ts';

type DateISOStringUnparsed = string;

type StationSearchResultItem = {
    id: number;
    name: string;
    address: string;
    location: {
        type: 'Point' | 'MultiPoint';
        coordinates: StationCoordinates;
    };
    company_id: number;
    created_at: DateISOStringUnparsed;
    updated_at: DateISOStringUnparsed;
};

export default StationSearchResultItem;
