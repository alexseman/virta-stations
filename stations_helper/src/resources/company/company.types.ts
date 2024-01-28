import Station from '@/resources/station/station.interface';

export type CompanyStationsGroup = {
    company_id: number;
    company_stations: Station[];
};
