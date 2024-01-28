import Station from '@/resources/station/station.interface';
import HttpException from '@/utils/exceptions/http.exception';
import * as process from 'process';

const VIRTA_API_HOST: string = process.env.VIRTA_API_HOST;

export class VirtaApiResponse {
    public data: object[] | object;
    public success: boolean;
    public status: number;

    constructor(
        data: object[] | object,
        success: boolean = true,
        status: number = 200
    ) {
        this.success = success;
        this.data = data;
        this.status = status;
    }
}

export class StationsVirtaApiResponse extends VirtaApiResponse {
    public data: Station[];

    constructor(
        data: Station[],
        success: boolean = true,
        status: number = 200
    ) {
        super(data, success, status);
    }
}

export const getAllStations = async (): Promise<StationsVirtaApiResponse> => {
    const response = await fetch(`${VIRTA_API_HOST}/stations?all`);

    if (!response.ok) {
        throw new HttpException(
            response.status,
            'Stations API Error - non 2xx response'
        );
    }

    // some validation for the received payload objects would be nice
    const responseBody: { data: Station[] } = await response.json();
    return new StationsVirtaApiResponse(responseBody.data);
};
