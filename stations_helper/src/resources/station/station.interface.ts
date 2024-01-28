type DateISOStringUnparsed = string;

type Station = {
    id: number;
    name: string;
    location: {
        type: 'Point' | 'MultiPoint';
        coordinates: number[];
    };
    company_id: number;
    created_at: DateISOStringUnparsed;
    updated_at: DateISOStringUnparsed;
};
export default Station;
