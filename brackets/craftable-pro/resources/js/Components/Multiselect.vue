<template>
    <FormControl
        :name="name"
        :label="label"
        :error="error"
        :label-placement="labelPlacement"
    >
        <div
            class="relative w-full rounded-md border shadow-sm focus-within:ring-1"
            :class="{ ...wrapperStyles, [inputClass]: true }"
        >
            <div
                v-if="leftIcon"
                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
            >
                <component
                    :is="leftIcon"
                    class="h-5 w-5 text-gray-400 dark:text-gray-500"
                    aria-hidden="true"
                />
            </div>
            <Multiselect
                v-model="value"
                :mode="mode"
                :limit="limit"
                :options="options"
                :placeholder="placeholder"
                :searchable="searchable"
                :label="optionsLabel"
                :value-prop="optionsValueProp"
                :track-by="optionsTrackBy"
                :hide-selected="hideSelected"
                :close-on-select="isSingle"
                autocomplete="nope"
                :required="required"
                @change="onChange"
                :classes="multiselectStyles"
                :disabled="disabled"
                :name="name"
                :create-option="createOption"
                :on-create="handleTagCreate"
                :object="object"
                :resolve-on-load="resolveOnLoad"
                :can-deselect="canDeselect"
                :can-clear="canClear"
                :allow-absent="allowAbsent"
                @search-change="emit('search-change', $event)"
            >
                <template #caret>
                    <span
                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md bg-white dark:bg-gray-700 pr-1.5"
                    >
                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                    </span>
                </template>
                <template #clear="{ clear }">
                    <button
                        type="button"
                        class="absolute inset-y-0 right-5 flex items-center bg-white dark:bg-gray-700 pr-0.5 focus:outline-none"
                        @click="clear"
                    >
                        <XMarkIcon class="h-4 w-4 text-gray-400  dark:text-gray-500" />
                    </button>
                </template>
                <template #tag="{ option, handleTagRemove, disabled }">
                    <slot
                        name="tag"
                        :option="option"
                        :handle-tag-remove="handleTagRemove"
                        :disabled="disabled"
                    >
                        <Tag
                            :dissmisable="true"
                            @dismiss="(event) => handleTagRemove(option, event)"
                        >
                            {{
                                optionsLabel && option[optionsLabel]
                                    ? option[optionsLabel]
                                    : option
                            }}
                        </Tag>
                    </slot>
                </template>
                <template #option="{ option, search }">
                    <slot
                        name="option"
                        :option="option"
                        :search="search"
                    />
                </template>
                <template #singlelabel="{ value }">
                    <div class="multiselect-single-label text-gray-800  dark:text-gray-100 text-sm">
                        <slot
                            name="singlelabel"
                            :value="value"
                        >
                            <template v-if="value[optionsLabel] || value[optionsValueProp]">
                                {{ value[optionsLabel] ?? value[optionsValueProp] }}
                            </template>
                            <template v-else-if="value.hasOwnProperty(optionsValueProp)">
                                {{ value[optionsValueProp] }}
                            </template>
                            <template v-else>
                                {{ value }}
                            </template>
                        </slot>
                    </div>
                </template>
                <template #multiplelabel="{ values }">
                    <div class="multiselect-multiple-label text-gray-800 dark:text-gray-100 text-sm">
                        <slot
                            name="multiplelabel"
                            :values="values"
                        >
                            {{
                                $tChoice(
                                    "craftable-pro",
                                    "{1} :count option selected|[2,*] :count options selected",
                                    values.length
                                )
                            }}
                        </slot>
                    </div>
                </template>
            </Multiselect>
            <div
                v-if="error"
                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-8"
            >
                <ExclamationCircleIcon
                    class="h-5 w-5 text-red-500"
                    aria-hidden="true"
                />
            </div>
        </div>
    </FormControl>
</template>

<script setup lang="ts">
import { computed } from "vue";
import type { Component } from "vue";
import Multiselect from "@vueform/multiselect";
import {
  ExclamationCircleIcon,
  ChevronUpDownIcon,
  XMarkIcon,
} from "@heroicons/vue/20/solid";
import { useInput } from "craftable-pro/hooks/useInput";
import { Tag, FormControl } from ".";

interface Props {
  name: string;
  label?: string;
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
  options: any[];
  searchable?: boolean;
  optionsLabel?: string;
  optionsValueProp?: string;
  optionsTrackBy?: string;
  hideSelected?: boolean;
  mode?: "tags" | "single" | "multiple";
  limit?: number;
  onChange?: (value: any) => void;
  handleTagCreate?: (option: any, select$: any) => void;
  // TODO: work this out
  modelValue?: any;
  createOption?: boolean;
  object?: boolean;
  resolveOnLoad?: boolean;
  canDeselect?: boolean;
  canClear?: boolean;
  labelPlacement?: "top" | "left";
  inputClass?: string;
  leftIcon?: Component;
    allowAbsent?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  required: false,
  disabled: false,
  searchable: true,
  hideSelected: false,
  mode: "multiple",
  optionsLabel: "label",
  optionsValueProp: "value",
  createOption: false,
  object: false,
  resolveOnLoad: true,
  labelPlacement: "top",
  inputClass: "",
    allowAbsent: false,
});

const emit = defineEmits(["update:modelValue", "change", "search-change"]);
const isSingle = computed(() => props.mode === "single");
const { value, error } = useInput(props, emit);

const wrapperStyles = computed(() => ({
  "border-red-300 focus-within:border-red-500 focus-within:ring-red-500":
    !!error.value,
  "border-gray-300 dark:border-gray-600 focus-within:border-primary-500 focus-within:ring-primary-500":
    !error.value,
}));

const multiselectStyles = {
    container: `mx-auto w-full flex items-center justify-end box-border cursor-pointer rounded-md bg-white dark:bg-gray-800 text-base leading-none outline-none py-0 min-h-[36px] ${
        props.leftIcon ? "pl-10" : ""
    }`,
    containerDisabled: "cursor-default bg-gray-100 dark:bg-gray-700",
    containerOpen: "",
    containerOpenTop: "",
    containerActive: "ring-0 ring-transparent border-transparent",
    singleLabel:
        "flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent pl-3 pr-10 max-w-full text-ellipsis overflow-hidden whitespace-nowrap text-sm h-[36px] leading-[36px]",
    multipleLabel:
        "flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent pl-3 text-sm h-[36px] leading-[36px]",
    search:
        "w-full absolute inset-0 outline-none appearance-none box-border border-0 font-sans bg-white dark:bg-gray-800 pl-3 block w-full pr-10 focus:outline-none ring-0 sm:text-sm rounded-md focus:ring-transparent focus:border-0 focus:border-transparent",
    tags: "flex-grow flex-shrink flex-wrap flex items-center gap-1 py-1 pl-2",
    tag: "bg-green-500 text-white dark:text-gray-800 text-sm font-semibold py-0.5 pl-2 rounded mr-1 mb-1 flex items-center whitespace-nowrap",
    tagDisabled: "pr-2 opacity-50",
    tagRemove:
        "flex items-center justify-center p-1 mx-0.5 rounded-sm hover:bg-black hover:bg-opacity-10 group",
    tagRemoveIcon:
        "bg-center bg-no-repeat opacity-30 inline-block w-3 h-3 group-hover:opacity-60",
    tagsSearchWrapper: "inline-block relative mx-1 flex-grow flex-shrink h-full",
    tagsSearch:
        "absolute inset-0 border-0 outline-none appearance-none p-0 text-base font-sans box-border w-full !ring-0 dark:text-gray-100 dark:bg-gray-800",
    tagsSearchCopy: "invisible whitespace-pre-wrap inline-block h-px",
    placeholder:
        "flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3 text-gray-600 dark:text-gray-400 text-sm",
    caret:
        "bg-center bg-no-repeat w-2.5 h-4 py-px box-content mr-3.5 relative z-10 opacity-40 flex-shrink-0 flex-grow-0 transition-transform transform pointer-events-auto",
    caretOpen: "pointer-events-auto",
    clear:
        "pr-3.5 relative z-10 opacity-40 transition duration-300 flex-shrink-0 flex-grow-0 flex hover:opacity-80",
    clearIcon: "bg-center bg-no-repeat w-2.5 h-4 py-px box-content inline-block",
    spinner:
        "bg-multiselect-spinner bg-center bg-no-repeat w-4 h-4 z-10 mr-3.5 animate-spin flex-shrink-0 flex-grow-0",
    dropdown:
        "max-h-60 absolute -left-px -right-px -bottom-2 transform translate-y-full -mt-px overflow-y-scroll z-40 bg-white dark:bg-gray-800 flex flex-col rounded-md shadow-lg z-10 max-h-60 py-1 text-base border border-gray-300 dark:border-gray-600 overflow-auto focus:outline-none sm:text-sm",
    dropdownTop:
        "-translate-y-full top-px bottom-auto flex-col-reverse rounded-b-none rounded-t",
    dropdownHidden: "hidden",
    options: "flex flex-col p-0 m-0 list-none text-gray-700 dark:text-gray-100",
    optionsTop: "flex-col-reverse",
    group: "p-0 m-0",
    groupLabel:
        "flex text-sm box-border items-center justify-start text-left py-1 px-3 font-semibold bg-gray-200 dark:bg-gray-700 cursor-default leading-normal",
    groupLabelPointable: "cursor-pointer",
    groupLabelPointed: "bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-100",
    groupLabelSelected: "bg-primary-500 text-white",
    groupLabelDisabled:
        "bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 cursor-not-allowed",
    groupLabelSelectedPointed: "bg-primary-500 text-white opacity-90",
    groupLabelSelectedDisabled:
        "text-green-100 bg-primary-500 bg-opacity-50 cursor-not-allowed",
    groupOptions: "p-0 m-0",
    option:
        "flex items-center justify-start box-border text-left cursor-pointer block px-4 py-2 text-sm",
    optionPointed: "text-gray-900 dark:text-gray-200 bg-gray-100 dark:bg-gray-600",
    optionSelected: "text-white bg-primary-500",
    optionDisabled: "!text-gray-300 cursor-not-allowed",
    optionSelectedPointed: "text-white bg-primary-500 opacity-90",
    optionSelectedDisabled:
        "text-white bg-primary-500 bg-opacity-50 cursor-not-allowed",
    noOptions:
        "py-2 px-3 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800",
    noResults:
        "py-2 px-3 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800",
    fakeInput:
        "bg-transparent absolute left-0 right-0 -bottom-px w-full border-0 p-0 appearance-none outline-none text-transparent -z-50",
    spacer: "h-8 py-0.5 box-content",
};

</script>
