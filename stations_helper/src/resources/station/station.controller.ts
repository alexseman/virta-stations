import { NextFunction, Request, Response, Router } from 'express';
import Controller from '@/utils/interfaces/controller.interface';

import StationService from '@/resources/station/station.service';
import { CompanyStationsGroup } from '@/resources/company/company.types';

class StationController implements Controller {
    public path = '/stations';
    public router = Router();
    private StationService = new StationService();

    constructor() {
        this.initializeRoutes();
    }

    private initializeRoutes(): void {
        this.router.get(`${this.path}/by-company`, this.getByCompany);
    }

    private getByCompany = async (
        req: Request,
        res: Response,
        next: NextFunction
    ): Promise<Response | void> => {
        try {
            const stations: CompanyStationsGroup[] =
                await this.StationService.byCompany();

            res.status(200).send({ success: true, data: stations });
        } catch (e) {
            next(e);
        }
    };
}

export default StationController;
