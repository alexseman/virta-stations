import Map, { Marker } from 'react-map-gl';
import StationSearchResultItem from '../types/StationSearchResultItem.ts';
import StationsMapPin from './StationsMapPin.tsx';
import { Popup } from 'mapbox-gl'; // should be in an ENV var

// should be in an ENV var
const MAPBOX_ACCESS_TOKEN =
    'pk.eyJ1IjoiYWxleGlwaW5mbyIsImEiOiJjbGJna3QwNnkwZ3VkM3Bwcm9qM3ZvamM3In0.--Cb8X8iewG-nuruRuyqcQ';

interface MapProps {
    stations: StationSearchResultItem[];
}

const StationsMap: React.FC<MapProps> = ({ stations }) => {
    return (
        <>
            <Map
                mapboxAccessToken={MAPBOX_ACCESS_TOKEN}
                initialViewState={{
                    longitude: 8.7,
                    latitude: 50.1,
                    zoom: 2,
                }}
                projection={{
                    name: 'mercator',
                }}
                style={{ width: '100%', height: 400, marginTop: '2rem' }}
                mapStyle="mapbox://styles/mapbox/light-v11"
            >
                {stations.length
                    ? stations.map((station) => (
                          <Marker
                              key={`marker-${station.company_id}-${station.id}`}
                              longitude={station.location.coordinates[0]}
                              latitude={station.location.coordinates[1]}
                              anchor="bottom"
                              popup={new Popup({})
                                  .setLngLat(station.location.coordinates)
                                  .setText(station.name)}
                          >
                              <StationsMapPin />
                          </Marker>
                      ))
                    : ''}
            </Map>
        </>
    );
};

export default StationsMap;
