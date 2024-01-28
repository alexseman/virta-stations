import useFormInput from '../hooks/useFormInput.ts';
import StationSearchResult from '../types/StationSearchResult.ts';
import { SetStateAction } from 'react';
import StationSearchResultItem from '../types/StationSearchResultItem.ts';
import { TailSpin } from 'react-loader-spinner';
import ApiErrorResult from '../types/ApiErrorResult.ts';

// should be ENV vars:
const API_HOST: string = 'http://localhost:4040';
const API_PATH: string = '/api/stations/search/';

interface LayoutProps {
    loading: boolean;
    setStations: React.Dispatch<SetStateAction<StationSearchResultItem[]>>;
    setLoading: React.Dispatch<SetStateAction<boolean>>;
    setError: React.Dispatch<SetStateAction<ApiErrorResult | null>>;
}

const StationsSearchForm: React.FC<LayoutProps> = ({
    loading,
    setStations,
    setLoading,
    setError,
}) => {
    const startingPointLatitudeProps = useFormInput(8.7);
    const startingPointLongitudeProps = useFormInput(50.1);
    const startingPointRadiusProps = useFormInput(500);

    const handleSubmit = (ev: React.FormEvent<HTMLFormElement>) => {
        ev.preventDefault();
        setLoading(true);
        setError(null);

        const abortController: AbortController = new AbortController();
        const url: string = `${API_HOST}${API_PATH}?lat=${startingPointLatitudeProps.value}&long=${startingPointLongitudeProps.value}&radius=${startingPointRadiusProps.value}`;
        const options = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            signal: abortController.signal,
        };
        const errorMessage: string = `${options.method.toUpperCase()} "${url}" API call did not return a 2xx code`;

        fetch(url, options)
            .then((r) => {
                if (!r.ok) {
                    throw new Error(errorMessage);
                }

                return r;
            })
            .then((r) => {
                return r.json();
            })
            .then((parsedResponse: StationSearchResult) => {
                setStations(parsedResponse.data);
            })
            .catch((err) => {
                if (!abortController.signal.aborted) {
                    err.success = false;
                    setError(err);
                }
            })
            .finally(() => {
                setLoading(false);
                abortController.abort();
            });
    };

    return (
        <>
            <section className="bg-white dark:bg-gray-900">
                <div className="py-8 px-4 mx-auto max-w-2xl lg:py-16">
                    <form onSubmit={handleSubmit}>
                        <div className="grid gap-4 sm:grid-cols-2 sm:gap-6">
                            <div className="w-full">
                                <label
                                    htmlFor="lat"
                                    className="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    Starting Point Latitude
                                </label>
                                <input
                                    type="number"
                                    step="0.1"
                                    min="-90"
                                    max="90"
                                    value={startingPointLatitudeProps.value}
                                    onChange={
                                        startingPointLatitudeProps.onChange
                                    }
                                    name="lat"
                                    id="lat"
                                    className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Starting Point Latitude"
                                />
                            </div>
                            <div className="w-full">
                                <label
                                    htmlFor="long"
                                    className="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    Starting Point Longitude
                                </label>
                                <input
                                    type="number"
                                    step="0.1"
                                    min="-180"
                                    value={startingPointLongitudeProps.value}
                                    onChange={
                                        startingPointLongitudeProps.onChange
                                    }
                                    max="180"
                                    name="long"
                                    id="long"
                                    className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Starting Point Longitude"
                                />
                            </div>
                        </div>
                        <div className="w-full relative my-12">
                            <label
                                htmlFor="labels-range-input"
                                className="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Search Radius in KM from Starting Point
                            </label>
                            <input
                                id="labels-range-input"
                                type="range"
                                value={startingPointRadiusProps.value}
                                onChange={startingPointRadiusProps.onChange}
                                min="10"
                                max="8000"
                                className="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                            />
                            <span className="text-sm text-gray-500 dark:text-gray-400 absolute start-0 -bottom-6">
                                Min (10km)
                            </span>
                            <span className="text-sm text-gray-500 dark:text-gray-400 absolute start-1/3 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">
                                2000km
                            </span>
                            <span className="text-sm text-gray-500 dark:text-gray-400 absolute start-2/3 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">
                                6000km
                            </span>
                            <span className="text-sm text-gray-500 dark:text-gray-400 absolute end-0 -bottom-6">
                                Max (8000km)
                            </span>
                        </div>
                        {!loading ? (
                            <button
                                type="submit"
                                className="inline-flex items-center px-12 py-2.5 mt-8 text-sm font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800"
                            >
                                Search
                            </button>
                        ) : (
                            <TailSpin
                                width="50"
                                height="50"
                                color="#2563eb"
                                wrapperClass="pt-4 justify-center"
                            />
                        )}
                    </form>
                </div>
            </section>
        </>
    );
};

export default StationsSearchForm;
