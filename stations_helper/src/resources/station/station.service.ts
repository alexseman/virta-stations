import Station from '@/resources/station/station.interface';
import { getAllStations, StationsVirtaApiResponse } from '@/wrappers/api/virta';
import { groupBy } from 'lodash';
import { CompanyStationsGroup } from '@/resources/company/company.types';

class StationService {
    public async byCompany(): Promise<CompanyStationsGroup[]> {
        const stations: Station[] = await this.getAllStations();
        return this.groupStationsByCompanies(stations);
    }

    private async getAllStations(): Promise<Station[]> {
        const stationsApiResponse: StationsVirtaApiResponse =
            await getAllStations();

        return stationsApiResponse.data;
    }

    private groupStationsByCompanies(
        stations: Station[]
    ): CompanyStationsGroup[] {
        const grouped: {
            [id: string]: Station[];
        } = groupBy(stations, (station: Station) => station.company_id);

        return Object.keys(grouped).map((companyId: string) => {
            return {
                // companyId * 1 would suffice but TS doesn't like it
                company_id: parseInt(companyId),
                company_stations: grouped[companyId],
            };
        });
    }
}

export default StationService;
