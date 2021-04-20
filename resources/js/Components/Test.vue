<template>
    <div>
      <div class="flex">
        <div class="input-row">
          <label>Make:</label>
          <select @change="getArticles" v-model="form.make" class="input--select">
            <option :value="null">--SELECT MAKE--</option>
            <option v-for="(make, key) in makes" :key="key" :value="make">{{make}}</option>
          </select>
        </div>
        <div class="input-row">
          <label>Model:</label>
          <select @change="getArticles" :disabled="!form.make" v-model="form.model" class="input--select">
            <option :value="null">--SELECT MODEL--</option>
            <option v-for="(model, key) in currentModels" :key="key" :value="model.model">{{model.model}}</option>
          </select>
        </div>
        <div class="input-row">
          <label>Year:</label>
          <select @change="getArticles" placeholder="SELECT YEAR" :disabled="!form.make && !form.model" v-model="form.year" class="input--select">
            <option :value="null">--SELECT YEAR--</option>
            <option v-for="(year, key) in years" :key="key" :value="year">{{year}}</option>
          </select>
        </div>
      </div>
      <div class="test-results">
        <div class="text-center" v-if="!results.length">No results</div>
        <div v-if="results.length">
          <div v-for="result in results" :key="result.id">{{result.joomla_url}}</div>
        </div>
        
      </div>
    </div>
</template>

<script>
export default {
  data() {
    return {
      years: [2010,2011,2012,2013,2014,2015,2016,2017,2018,2019,2020,2021],
      makes: ['ford', 'chevrolet', 'honda'],
      models: [{make: 'ford', model: 'fusion'}, {make: 'chevrolet', model: 'equinox'}, {make: 'honda', model: 'civic'}],
      form: {
        make: null,
        model: null,
        year: null
      },
      results: []
    }
  },
  methods: {
    getArticles() {
      let keys = Object.keys(this.form)
      let params = {}
      keys.forEach(k => {
        if(this.form[k]) {
          params[k] = this.form[k]
        }
      })
      console.log(params)
      axios.get('/api/search/articles', {
        params: params,
      })
      .then(res => {
        console.log(res)
        this.results = res.data.data
      })
    },
  },
  computed: {
    currentModels() {
      let filtered = this.models.filter(m => m.make == this.form.make)
      return filtered.length ? filtered : []
    }
  }
}
</script>

<style>

</style>