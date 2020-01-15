<template>
    <label class="ui-switch" :class="classes">
        <div class="ui-switch__input-wrapper">
            <input
                class="ui-switch__input"
                ref="input"
                type="checkbox"

                :checked.prop="isChecked"
                :disabled="disabled"
                :name="name"
                :tabindex="tabindex"
                :value="submittedValue"

                @blur="onBlur"
                @click="onClick"
                @focus="onFocus"
            >

            <div class="ui-switch__thumb">
                <div class="ui-switch__focus-ring"></div>
            </div>

            <div class="ui-switch__track"></div>
        </div>

        <div class="ui-switch__label-text" v-if="label || $slots.default">
            <slot>{{ label }}</slot>
        </div>
    </label>
</template>

<script>
export default {
    name: 'ui-switch',
    props: {
        name: String,
        label: String,
        tabindex: [String, Number],
        value: {
            required: true
        },
        trueValue: {
            default: true
        },
        falseValue: {
            default: false
        },
        submittedValue: {
            type: String,
            default: 'on' // HTML default
        },
        checked: {
            type: Boolean,
            default: false
        },
        color: {
            type: String,
            default: 'primary' // 'primary' or 'accent'
        },
        switchPosition: {
            type: String,
            default: 'left' // 'left' or 'right'
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            isActive: false,
            isChecked: looseEqual(this.value, this.trueValue) || this.checked,
            initialValue: this.value
        };
    },
    computed: {
        classes() {
            return [
                `ui-switch--color-${this.color}`,
                `ui-switch--switch-position-${this.switchPosition}`,
                { 'is-active': this.isActive },
                { 'is-checked': this.isChecked },
                { 'is-disabled': this.disabled }
            ];
        }
    },
    watch: {
        value() {
            this.isChecked = looseEqual(this.value, this.trueValue);
        }
    },
    created() {
        this.$emit('input', this.isChecked ? this.trueValue : this.falseValue);
    },
    methods: {
        focus() {
            this.$refs.input.focus();
        },
        onClick(e) {
            const isCheckedPrevious = this.isChecked;
            const isChecked = e.target.checked;
            this.$emit('input', isChecked ? this.trueValue : this.falseValue, e);
            if (isCheckedPrevious !== isChecked) {
                this.$emit('change', isChecked ? this.trueValue : this.falseValue, e);
            }
        },
        onFocus(e) {
            this.isActive = true;
            this.$emit('focus', e);
        },
        onBlur(e) {
            this.isActive = false;
            this.$emit('blur', e);
        }
    }
};
</script>