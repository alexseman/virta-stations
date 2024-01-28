import './App.css';
import Footer from './components/Footer.tsx';
import Header from './components/Header.tsx';
import StationsSearchForm from './components/StationsSearchForm.tsx';
import { useState } from 'react';
import StationSearchResultItem from './types/StationSearchResultItem.ts';
import StationsMap from './components/StationsMap.tsx';
import Alert from './components/Alert.tsx';
import ApiErrorResult from './types/ApiErrorResult.ts';

function App() {
    const [stations, setStations] = useState([] as StationSearchResultItem[]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<ApiErrorResult | null>(null);

    return (
        <>
            <Header></Header>
            <main className={`${loading ? 'cursor-progress' : ''}`}>
                {error && error.message ? (
                    <Alert message={error.message} />
                ) : (
                    <div className={`${loading ? 'opacity-20' : ''}`}>
                        <StationsMap stations={stations}></StationsMap>
                    </div>
                )}

                <StationsSearchForm
                    loading={loading}
                    setStations={setStations}
                    setLoading={setLoading}
                    setError={setError}
                />
            </main>
            <Footer></Footer>
        </>
    );
}

export default App;
