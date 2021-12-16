import axios from './index'

export default {
    getAsteroidData(param) {
        let data  = param;
        return axios.get('get-asteroid-data', {
            params: data,
            headers: {                    
            }
        })
    },
}