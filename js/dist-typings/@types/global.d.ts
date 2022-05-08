declare type Writable<T> = { -readonly [P in keyof T]: T[P] };
declare type DeepWritable<T> = { -readonly [P in keyof T]: DeepWritable<T[P]> };

declare type DeepReadonly<T> = { readonly [P in keyof T]: DeepReadonly<T[P]> };

/**
 * UTILITY TYPES
 */

/**
 * Type that returns an array of all keys of a provided object that are of
 * of the provided type, or a subtype of the type.
 */
declare type KeysOfType<Type extends object, Match> = {
  [Key in keyof Type]-?: Type[Key] extends Match ? Key : never;
};

/**
 * Type that matches one of the keys of an object that is of the provided
 * type, or a subtype of it.
 */
declare type KeyOfType<Type extends object, Match> = KeysOfType<Type, Match>[keyof Type];

type Component<A> = import('mithril').Component<A>;

declare type ComponentClass<Attrs = Record<string, unknown>, C extends Component<Attrs> = Component<Attrs>> = {
  new (...args: any[]): Component<Attrs>;
  prototype: C;
};

/**
 * Unfortunately, TypeScript only supports strings and classes for JSX tags.
 * Therefore, our type definition should only allow for those two types.
 *
 * @see https://github.com/microsoft/TypeScript/issues/14789#issuecomment-412247771
 */
declare type VnodeElementTag<Attrs = Record<string, unknown>, C extends Component<Attrs> = Component<Attrs>> = string | ComponentClass<Attrs, C>;

/**
 * @deprecated Please import `app` from a namespace instead of using it as a global variable.
 *
 * @example App in forum JS
 * ```
 * import app from 'duroom/forum/app';
 * ```
 *
 * @example App in admin JS
 * ```
 * import app from 'duroom/admin/app';
 * ```
 *
 * @example App in common JS
 * ```
 * import app from 'duroom/common/app';
 * ```
 */
declare const app: never;

declare const m: import('mithril').Static;
declare const dayjs: typeof import('dayjs');

/**
 * From https://github.com/lokesh/color-thief/issues/188
 */
declare module 'color-thief-browser' {
  type Color = [number, number, number];
  export default class ColorThief {
    getColor: (img: HTMLImageElement | null) => Color;
    getPalette: (img: HTMLImageElement | null) => Color[];
  }
}

type ESModule = { __esModule: true; [key: string]: unknown };

/**
 * The global `duroom` variable.
 *
 * Contains the compiled ES Modules for all DuRoom extensions and core.
 *
 * @example <caption>Check if `duroom-tags` is present</captions>
 * if ('duroom-tags' in duroom.extensions) {
 *   // Tags is installed and enabled!
 * }
 */
interface DuRoomObject {
  /**
   * Contains the compiled ES Module for DuRoom's core.
   *
   * You shouldn't need to access this directly for any reason.
   */
  core: Readonly<ESModule>;
  /**
   * Contains the compiled ES Modules for all DuRoom extensions.
   *
   * @example <caption>Check if `duroom-tags` is present</captions>
   * if ('duroom-tags' in duroom.extensions) {
   *   // Tags is installed and enabled!
   * }
   */
  extensions: Readonly<Record<string, ESModule>>;
}

declare const duroom: DuRoomObject;

// Extend JQuery with our custom functions, defined with $.fn
interface JQuery {
  /**
   * DuRoom's tooltip JQuery plugin.
   *
   * Do not use this directly. Instead use the `<Tooltip>` component that
   * is exported from `duroom/common/components/Tooltip`.
   *
   * This will be removed in a future version of DuRoom.
   *
   * @deprecated
   */
  tooltip: import('./tooltips/index').TooltipJQueryFunction;
}

/**
 * For more info, see: https://www.typescriptlang.org/docs/handbook/jsx.html#attribute-type-checking
 *
 * In a nutshell, we need to add `ElementAttributesProperty` to tell Typescript
 * what property on component classes to look at for attribute typings. For our
 * Component class, this would be `attrs` (e.g. `this.attrs...`)
 */
interface JSX {
  ElementAttributesProperty: {
    attrs: Record<string, unknown>;
  };
}

interface Event {
  /**
   * Whether this event should trigger a Mithril redraw.
   */
  redraw: boolean;
}
