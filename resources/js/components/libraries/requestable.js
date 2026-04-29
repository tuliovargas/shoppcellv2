import axios from 'axios'

class Requestable {
    constructor(url, options) {
        this.url = url
        this.requestConfig = options.requestConfig
        this.onSuccess = options.onSuccess
        this.onError = options.onError
        this.axios = axios
        this.loading = false
        this.response = {}
    }

    setUrl (url) {
        this.url = url
    }

    request () {
        this.loading = true;

        this.axios.request({
            url: this.url, ...this.requestConfig
        })
        .then((response) => {

            this.response = response
            this.loading = false
            if (this.onSuccess !== undefined) {
                this.onSuccess(this.response)
            }
        })
        .catch((error) => {
            this.response = error;
            this.loading = false;
            if (this.onError !== undefined) this.onError(this.response)
        });

        return this;
    }
}

export default Requestable
