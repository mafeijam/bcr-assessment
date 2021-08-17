<template lang="pug">
q-layout(view="hHh lpr fFf")
  q-page-container.bg-grey-1
    q-page(padding style="max-width: 1500px; margin: auto")
      q-card.shadow-1(style="min-height: calc(100vh - 48px);")
        q-card-section
          .flex.justify-between
            .text-h5.text-indigo.q-mb-sm Today's Available Limit
            .text-h6.text-weight-bold(:class="textColor") {{ remain }} <span class="text-grey-7">/ 500mg</span>
          q-linear-progress(size="20px" :value="limit" :color="color")

        q-card-section
          .text-h6.q-mb-sm Favorite Caffeinated Drinks
          .row.q-col-gutter-md
            div(v-for="drink in drinks" :class="drinkRowSize")
              q-card.shadow-1
                .q-pa-md
                  q-img(
                    :src="`/images/${drink.name}.jpg`"
                    :height="$q.screen.gt.sm ? '150px' : '100px'"
                    fit="contain" no-spinner
                  )
                q-card-section.q-gutter-sm
                  div(:class="$q.screen.gt.sm ? 'text-h6' : 'text-subtitle1'") {{ drink.name }}
                  .flex.justify-between.items-baseline
                    .text-body2.text-blue-grey-8 Caffeine
                    .text-body1.text-blue-8.text-weight-bold {{ drink.caffeine }}mg
                  .flex.justify-between.items-baseline
                    .text-body2.text-blue-grey-8 Today consumed
                    .text-body1.text-blue-8.text-weight-bold {{ consumedCount[drink.id] || 0 }}
                  .q-pt-md
                  .text-caption.text-blue-grey-5 You can have <span class="text-blue-8 text-body1 text-weight-bold">{{ howManyMore(drink) }}</span> more to stay in a safe limit
                q-card-actions(align="right")
                  q-btn(flat color="blue" @click="consume(drink)") consume

        q-card-section
          .text-h6.q-mb-sm Consumed Drinks
          q-table.shadow-1(:rows="records" :columns="columnsRecord" :pagination="paginationRecord" row-key="name" dense)
            template(#body-cell-action="props")
              q-td(:props="props")
                q-btn(icon="close" flat dense round size="sm" color="pink" @click="remove(props.row)")
                  q-tooltip remove

        q-card-section
          .text-h6.q-mb-sm Daily Summary
          q-table.shadow-1(:rows="dailySummary" :columns="columnsSummary" :pagination="paginationSummary" row-key="date" dense)
</template>

<script>
import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { useQuasar } from 'quasar'

export default {
  props: {
    drinks: Array,
    records: Array,
    consumed: Number,
    consumedCount: Array,
    dailySummary: Array
  },
  setup(props) {
    const $q = useQuasar()

    const flash = computed(() => usePage().props.value.flash)
    const remain = computed(() => 500 - props.consumed)
    const limit = computed(() => remain.value < 0 ? 0 : remain.value / 500)
    const screen = computed(() => $q.screen.name)

    watch(flash, flash => {
      if (flash !== null) {
        $q.dialog({
          title: flash.title,
          message: flash.message,
          class: flash.color,
          html: true
        })
      }
    })


    const color = ref('green')
    const textColor = ref('text-green')

    watch(remain, remain => {
      if (remain <= 150) {
        color.value = 'red'
        textColor.value = 'text-red'
      } else if (remain <= 350) {
        color.value = 'orange'
        textColor.value = 'text-orange'
      } else {
        color.value = 'green'
        textColor.value = 'text-green'
      }
    }, {
      immediate: true
    })


    const drinkRowSize = ref('col')

    watch(screen, screen => {
      if (screen === 'xs') {
        drinkRowSize.value = 'col-12'
      } else if (screen === 'md' || screen === 'sm') {
        drinkRowSize.value = 'col-6'
      } else {
        drinkRowSize.value = 'col'
      }
    }, {
      immediate: true
    })

    const columnsRecord = [
      { name: 'date', align: 'left', label: 'Date', field: 'date', sortable: true },
      { name: 'drink', align: 'left', label: 'Drink', field: r => r.drink.name, sortable: true },
      { name: 'caffeine', align: 'right', label: 'Caffeine', field: r => r.drink.caffeine, sortable: true },
      { name: 'action', label: 'Action', sortable: false }
    ]

    const paginationRecord = {
      rowsPerPage: 10
    }

    const columnsSummary = [
      { name: 'date', align: 'left', label: 'Date', field: 'date', sortable: true },
      { name: 'count', align: 'right', label: 'Count', field: 'count', sortable: true },
      { name: 'caffeine', align: 'right', label: 'Caffeine', field: 'caffeine', sortable: true },
    ]

    const paginationSummary = {
      rowsPerPage: 10
    }

    const consume = drink => Inertia.post(`/drink/${drink.id}`)
    const remove = drink => Inertia.delete(`/drink/${drink.id}`)
    const howManyMore = drink => Math.floor(remain.value / drink.caffeine)

    return {
      limit,
      remain,
      drinkRowSize,
      color,
      textColor,
      flash,
      columnsRecord,
      columnsSummary,
      paginationRecord,
      paginationSummary,
      consume,
      remove,
      howManyMore,
    }
  }
}
</script>
